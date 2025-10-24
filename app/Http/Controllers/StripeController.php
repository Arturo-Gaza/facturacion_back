<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Models\CatMontosPrepago;
use App\Models\MovimientoSaldo;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function createPaymentIntent(Request $req)
    {
        $amount = $req->input('amount'); // en centavos: $10 USD => 1000
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $amount,
            'currency' => $req->input('currency', 'usd'),
            // opcional: 'metadata' => ['order_id' => 1234]
        ]);

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    }

    public function crearPagoByPrepago(Request $req)
    {
        try {
            $id_user = $req->input('id_user');;
            $idPrepago = $req->input('idPrepago'); // en centavos: $10 USD => 1000
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $prepago = CatMontosPrepago::find($idPrepago);
            $amount = $prepago->monto * 100;
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => env('DIVISA'),
                'metadata' => [
                    'user_id' => $id_user,
                    'prepago_id' => $prepago->id,
                    'origin' => 'app_mobile'
                ],
                'payment_method_types' => ['card']
                // opcional: 'metadata' => ['order_id' => 1234]
            ]);

            $stripeCustomerId = $paymentIntent->customer ?? null;
            $stripePaymentMethod = $paymentIntent->payment_method ?? null;

            // Si ya viene un cargo asociado (no es obligatorio en la creación del PI)
            $stripeChargeId = null;
            if (!empty($paymentIntent->charges) && !empty($paymentIntent->charges->data) && isset($paymentIntent->charges->data[0]->id)) {
                $stripeChargeId = $paymentIntent->charges->data[0]->id;
            }

            // idempotency key (si el cliente la envió)
            $idempotencyKey = $req->header('Idempotency-Key') ?? $req->header('Idempotency-Key') ?? null;

            // obtener usuario actual (para calcular nuevo_monto estimado)
            $user = User::find($id_user);
            // si tu columna de saldo tiene otro nombre, cámbialo aquí
            $currentSaldo = $user ? (float) $user->saldo : 0.00;

            // monto decimal en unidad monetaria (positivo = pago / recarga)
            $montoDecimal = $amount / 100;

            // Estatus pendiente = 1 (según tu requerimiento)
            $estatusPendienteId = 1;

            DB::beginTransaction();

            $mov = MovimientoSaldo::create([
                'tipo' => "abono",
                'usuario_id' => $id_user,
                'monto' => $montoDecimal, // según tu convención: positivo para pago/recarga
                'currency' => $paymentIntent->currency ?? env('DIVISA', 'mxn'),
                'amount_cents' => $amount,
                'estatus_movimiento_id' => $estatusPendienteId,
                'saldo_resultante' => $currentSaldo,
                'descripcion' => "Prepago id {$prepago->id} - creación de PaymentIntent",
                'payment_intent_id' => $paymentIntent->id,
                'stripe_charge_id' => $stripeChargeId,
                'customer_id' => $stripeCustomerId,
                'payment_method' => $stripePaymentMethod,
                'metadata' => $paymentIntent->metadata ? json_decode(json_encode($paymentIntent->metadata), true) : null,
                'stripe_event_id' => null,
                'webhook_payload' => null,
                'processed_at' => null,
                'failure_code' => null,
                'failure_message' => null,
                'idempotency_key' => $idempotencyKey,
                'refunded_amount' => 0,
                'reverted' => false,
            ]);


            DB::commit();


            return ApiResponseHelper::sendResponse($paymentIntent->client_secret, 'Pago creado correctamente para el plan de prepago.', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, $ex->getMessage(), 500);
        }
    }

    public function confirmStripePayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        $paymentIntentId = $request->input('payment_intent_id');

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Recuperamos el PaymentIntent y expandimos charges para obtener charge id si existe
            $pi = PaymentIntent::retrieve($paymentIntentId, ['expand' => ['charges']]);

            if (!$pi || empty($pi->id)) {
                return ApiResponseHelper::rollback(null,'PaymentIntent no encontrado en Stripe',404);
            }

            // Buscar movimiento local
            $mov = MovimientoSaldo::where('payment_intent_id', $paymentIntentId)->first();

            if (!$mov) {
                return ApiResponseHelper::rollback(null,'Movimiento no encontrado en DB',404);
            }

            // Si ya fue procesado (idempotencia)
            if ($mov->processed_at) {
                return ApiResponseHelper::rollback(null,'Movimiento no encontrado en DB',404);
            }

            // Validaciones de seguridad: que el monto coincida
            $stripeAmount = $pi->amount ?? null; // centavos
            $localAmountCents = $mov->amount_cents ?? (int) round($mov->monto * 100);

            if ($stripeAmount !== null && $localAmountCents !== null && (int)$stripeAmount !== (int)$localAmountCents) {
                Log::warning("Mismatch amount: PI {$paymentIntentId} stripe {$stripeAmount} != local {$localAmountCents}");
                // Opcional: rechazar o marcar en discrepancia
                // return response()->json(['error' => 'Monto no coincide'], 400);
            }

            // Estado de stripe
            $status = $pi->status; // 'requires_payment_method' | 'requires_confirmation' | 'requires_action' | 'processing' | 'succeeded' | 'canceled'
            $stripeChargeId = null;
            if (!empty($pi->charges) && !empty($pi->charges->data) && isset($pi->charges->data[0]->id)) {
                $stripeChargeId = $pi->charges->data[0]->id;
            }

            DB::beginTransaction();

            // Si succeeded -> actualizar movimiento y saldo
            if ($status === 'succeeded') {
                // Estatus completado (ajusta según tu cat_estatus_movimiento)
                $estatusCompletado = 2;

                // actualizar movimiento
                $mov->stripe_charge_id = $stripeChargeId;
                $mov->customer_id = $pi->customer ?? $mov->customer_id;
                $mov->payment_method = $pi->payment_method ?? $mov->payment_method;
                $mov->currency = $pi->currency ?? $mov->currency;
                $mov->amount_cents = $pi->amount ?? $mov->amount_cents;
                $mov->metadata = isset($pi->metadata) ? json_decode(json_encode($pi->metadata), true) : $mov->metadata;
                $mov->stripe_event_id = null; // si no viene de webhook, no hay event id
                $mov->webhook_payload = json_encode($pi);
                $mov->processed_at = Carbon::now();
                $mov->failure_code = null;
                $mov->failure_message = null;
                $mov->estatus_movimiento_id = $estatusCompletado;

                // actualizar saldo de usuario
                $user = User::find($mov->usuario_id);
                if ($user) {
                    // Asumo monto positivo = recarga
                    $nuevoSaldo = (float)$user->saldo + ((float)$mov->monto);
                    $user->saldo = $nuevoSaldo;
                    $user->save();

                    $mov->saldo_resultante = $nuevoSaldo;
                }

                $mov->save();
                DB::commit();
            return ApiResponseHelper::sendResponse($nuevoSaldo, 'Pago confirmado y saldo actualizado.', 200);

            }

            // Si está en otro estado (processing, requires_action, canceled...)
            DB::rollBack();
            return ApiResponseHelper::rollback(null,'PaymentIntent no está en estado succeeded' ,404);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error confirmStripePayment: ' . $e->getMessage());
            return ApiResponseHelper::rollback(null,'Error al verificar PaymentIntent'. 'detail' . $e->getMessage(),404);
        }
    }
}
