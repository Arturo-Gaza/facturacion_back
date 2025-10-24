<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\MovimientoSaldo;
use App\Models\User;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Stripe Webhook - Invalid payload: ' . $e->getMessage());
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe Webhook - Invalid signature: ' . $e->getMessage());
            return response('Invalid signature', 400);
        }

        // Guardar payload crudo para auditoría (opcional)
        $rawPayload = $payload;
        $stripeEventId = $event->id ?? null;
        $type = $event->type ?? '';

        try {
            // Manejo de payment_intent.succeeded
            if ($type === 'payment_intent.succeeded') {
                $pi = $event->data->object; // PaymentIntent object

                $paymentIntentId = $pi->id ?? null;

                if (!$paymentIntentId) {
                    Log::error("Stripe Webhook payment_intent.succeeded sin payment_intent id.");
                    return response('No payment_intent id', 400);
                }

                $mov = MovimientoSaldo::where('payment_intent_id', $paymentIntentId)->first();

                if (!$mov) {
                    // Si no encontramos el movimiento, opcional: crear uno o loggear.
                    Log::warning("Movimiento no encontrado para payment_intent_id: {$paymentIntentId}");
                    return response('Movimiento no encontrado', 200); // responder 200 para no reintentar webhook infinitamente
                }

                // Ids de estatus — ajusta según tu catálogo
                $estatusCompletado = 3; // <-- AJUSTA si tu id es distinto

                // Si ya fue procesado, evitamos duplicados (idempotencia)
                if ($mov->processed_at) {
                    Log::info("Movimiento ya procesado (id: {$mov->id}, payment_intent: {$paymentIntentId})");
                    return response('Already processed', 200);
                }

                // Extraer charge si existe
                $stripeChargeId = null;
                if (!empty($pi->charges) && !empty($pi->charges->data) && isset($pi->charges->data[0]->id)) {
                    $stripeChargeId = $pi->charges->data[0]->id;
                }

                DB::beginTransaction();
                try {
                    // Actualizar movimiento con datos de Stripe
                    $mov->stripe_charge_id = $stripeChargeId;
                    $mov->customer_id = $pi->customer ?? $mov->customer_id;
                    $mov->payment_method = $pi->payment_method ?? $mov->payment_method;
                    $mov->currency = $pi->currency ?? $mov->currency;
                    $mov->amount_cents = $pi->amount ?? $mov->amount_cents;
                    $mov->metadata = isset($pi->metadata) ? json_decode(json_encode($pi->metadata), true) : $mov->metadata;
                    $mov->stripe_event_id = $stripeEventId;
                    $mov->webhook_payload = $rawPayload;
                    $mov->processed_at = Carbon::now();
                    $mov->failure_code = null;
                    $mov->failure_message = null;
                    $mov->estatus_movimiento_id = $estatusCompletado;

                    // Actualizar saldo de usuario
                    $user = User::find($mov->usuario_id);
                    if ($user) {
                        // Asumo que 'monto' es positivo para recarga; si en tu app es negativo para cobro, ajusta.
                        $nuevoSaldo = (float)$user->saldo + (float)$mov->monto;
                        $user->saldo = $nuevoSaldo;
                        $user->save();

                        // Actualizar nuevo_monto en movimiento
                        $mov->saldo_resultante = $nuevoSaldo;
                    }

                    $mov->save();

                    DB::commit();

                    Log::info("Checkout pago confirmado, movimiento actualizado id: {$mov->id}, payment_intent: {$paymentIntentId}");
                    return response('Webhook handled', 200);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Error procesando payment_intent.succeeded: " . $e->getMessage());
                    return response('Internal error', 500);
                }
            }

            // Manejo de payment_intent.payment_failed
            if ($type === 'payment_intent.payment_failed') {
                $pi = $event->data->object;
                $paymentIntentId = $pi->id ?? null;

                $mov = MovimientoSaldo::where('payment_intent_id', $paymentIntentId)->first();
                if (!$mov) {
                    Log::warning("Movimiento no encontrado para payment_intent.payment_failed: {$paymentIntentId}");
                    return response('Movimiento no encontrado', 200);
                }

                // Id de estatus para fallo (ajusta)
                $estatusFallido = 3;

                DB::beginTransaction();
                try {
                    $mov->failure_code = $pi->last_payment_error->code ?? null;
                    $mov->failure_message = $pi->last_payment_error->message ?? null;
                    $mov->stripe_event_id = $stripeEventId;
                    $mov->webhook_payload = $rawPayload;
                    $mov->processed_at = Carbon::now();
                    $mov->estatus_movimiento_id = $estatusFallido;
                    $mov->save();

                    DB::commit();

                    Log::info("Payment failed procesado para movimiento id {$mov->id}");
                    return response('Webhook handled', 200);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Error procesando payment_intent.payment_failed: " . $e->getMessage());
                    return response('Internal error', 500);
                }
            }

            // Manejo de charge.refunded (cuando hay reembolso)
            if ($type === 'charge.refunded' || $type === 'charge.refund.updated') {
                $charge = $event->data->object;
                $chargeId = $charge->id ?? null;

                // intentar buscar por stripe_charge_id primero; si no hay, intentar por payment_intent
                $mov = MovimientoSaldo::where('stripe_charge_id', $chargeId)
                        ->orWhere('payment_intent_id', $charge->payment_intent ?? null)
                        ->first();

                if (!$mov) {
                    Log::warning("Movimiento no encontrado para charge.refunded: {$chargeId}");
                    return response('Movimiento no encontrado', 200);
                }

                DB::beginTransaction();
                try {
                    // calcular monto reembolsado total desde el objeto charge
                    $amountRefundedCents = $charge->amount_refunded ?? 0;
                    $amountRefunded = $amountRefundedCents / 100;

                    $mov->refunded_amount = $amountRefunded;
                    $mov->stripe_event_id = $stripeEventId;
                    $mov->webhook_payload = $rawPayload;
                    $mov->processed_at = Carbon::now();

                    // Si se reembolsó todo, marcar reverted y ajustar saldo del usuario
                    if (isset($charge->amount) && $amountRefundedCents >= $charge->amount) {
                        $mov->reverted = true;

                        $user = User::find($mov->usuario_id);
                        if ($user) {
                            // Restar el monto reembolsado del saldo del usuario
                            $user->saldo = max(0, (float)$user->saldo - $amountRefunded);
                            $user->save();

                            $mov->nuevo_monto = $user->saldo;
                        }
                    }

                    $mov->save();
                    DB::commit();

                    Log::info("Reembolso procesado para movimiento id {$mov->id}, refund: {$amountRefunded}");
                    return response('Webhook handled', 200);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Error procesando charge.refunded: " . $e->getMessage());
                    return response('Internal error', 500);
                }
            }

            // Otros eventos que quieras manejar: invoice.payment_succeeded, charge.dispute.created, etc.
            Log::info("Stripe Webhook recibido (no manejado explícitamente): {$type}");
            return response('Event type not handled', 200);
        } catch (\Exception $e) {
            Log::error('Error manejando webhook Stripe: ' . $e->getMessage());
            return response('Internal error', 500);
        }
    }
}
