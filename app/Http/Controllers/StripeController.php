<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Models\CatMontosPrepago;
use App\Models\MovimientoSaldo;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\DB;

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
            if (env('APP_ENV') === 'local') {
                $estatusCompletado = 3;
                $user = User::find($mov->usuario_id);
                if ($user) {
                    // Asumo que 'monto' es positivo para recarga; si en tu app es negativo para cobro, ajusta.
                    $nuevoSaldo = (float)$user->saldo + (float)$mov->monto;
                    $user->saldo = $nuevoSaldo;
                    $user->save();
                    $mov->estatus_movimiento_id = $estatusCompletado;
                    // Actualizar nuevo_monto en movimiento
                    $mov->saldo_resultante = $nuevoSaldo;
                    $mov->save();
                }
            }

            DB::commit();


            return ApiResponseHelper::sendResponse($paymentIntent->client_secret, 'Pago creado correctamente para el plan de prepago.', 200,$nuevoSaldo);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, $ex->getMessage(), 500);
        }
    }
}
