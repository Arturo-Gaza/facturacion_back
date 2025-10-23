<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Suscripciones;
use Carbon\Carbon;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            // invalid payload
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            // invalid signature
            return response('Invalid signature', 400);
        }

        // Maneja eventos relevantes
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // Asegurarse que el pago fue realizado
            if ($session->payment_status === 'paid') {
                // metadata
                $usuarioId = $session->metadata->usuario_id ?? null;
                $planId = $session->metadata->plan_id ?? null;
                $sessionId = $session->id;
                $paymentIntentId = $session->payment_intent ?? null;

                // Evitar duplicados: buscar por stripe_session_id o payment_intent
                $sus = Suscripciones::where('stripe_session_id', $sessionId)
                        ->orWhere('stripe_payment_intent_id', $paymentIntentId)
                        ->first();

                if (!$sus) {
                    // crear suscripción definitiva
                    $sus = Suscripciones::create([
                        'usuario_id' => $usuarioId,
                        'id_plan' => $planId,
                        'fecha_inicio' => now(),
                        'fecha_vencimiento' => Carbon::now()->addMonth(), // calcula según tu plan
                        'estado' => Suscripciones::ESTADO_ACTIVA,
                        'perfiles_utilizados' => 0,
                        'facturas_realizadas' => 0,
                        'stripe_session_id' => $sessionId,
                        'stripe_payment_intent_id' => $paymentIntentId,
                    ]);
                } else {
                    // actualizar estado en caso de que exista pendiente
                    $sus->update([
                        'estado' => Suscripciones::ESTADO_ACTIVA,
                        'stripe_payment_intent_id' => $paymentIntentId,
                        'fecha_inicio' => now(),
                    ]);
                }

                Log::info("Checkout pago confirmado, suscripcion creada/activada: " . ($sus->id ?? 'n/a'));
            }
        }

        // Otras acciones útiles: invoice.payment_succeeded, payment_intent.succeeded, etc.

        return response('Webhook handled', 200);
    }
}
