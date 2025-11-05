<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Models\CatMontosPrepago;
use App\Models\CatPlanes;
use App\Models\MovimientoSaldo;
use App\Models\Suscripciones;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\BalanceTransaction;
use Stripe\PaymentMethod;

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
            $id_user = $req->input('id_user');
            $idPrepago = $req->input('idPrepago'); // id del catálogo de montos
            Log::error(env('STRIPE_SECRET'));
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $prepago = CatMontosPrepago::find($idPrepago);
            if (!$prepago) {
                return null;
            }

            $amount = (int) ($prepago->monto * 100); // en centavos
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => env('DIVISA'),
                'metadata' => [
                    'user_id' => $id_user,
                    'prepago_id' => $prepago->id,
                    'origin' => 'web'
                ]
            ]);

            // datos retornados por stripe
            $stripeCustomerId = $paymentIntent->customer ?? null;
            $stripePaymentMethod = $paymentIntent->payment_method ?? null; // puede venir null

            // puede existir un charge ya (no obligatorio)
            $stripeChargeId = null;
            if (!empty($paymentIntent->charges) && !empty($paymentIntent->charges->data) && isset($paymentIntent->charges->data[0]->id)) {
                $stripeChargeId = $paymentIntent->charges->data[0]->id;
            }

            // idempotency key (si el cliente la envió)
            $idempotencyKey = $req->header('Idempotency-Key') ?? $req->header('Idempotency-Key') ?? null;

            // obtener usuario actual (para calcular saldo_antes)
            $user = User::find($id_user);
            $currentSaldo = $user ? (float) $user->saldo : 0.00;

            // monto decimal en unidad monetaria (positivo = recarga)
            $montoDecimal = $amount / 100;

            // Estatus pendiente = 1 (según tu requerimiento)
            $estatusPendienteId = 1;

            // campos extra a poblar
            $payment_method_type = null;
            $card_brand = null;
            $card_last4 = null;

            // Si viene payment_method (pm_xxx) intentamos recuperarlo para extraer card.brand y last4
            if (!empty($stripePaymentMethod)) {
                try {
                    $pm = PaymentMethod::retrieve($stripePaymentMethod);

                    $payment_method_type = $pm->type ?? null;

                    if (!empty($pm->card)) {
                        $card_brand = $pm->card->brand ?? null;
                        $card_last4 = $pm->card->last4 ?? null;
                    }
                } catch (\Exception $e) {
                    // No hacer fail la creación por esto; solo loguear
                    $payment_method_type = null;
                    $card_brand = null;
                    $card_last4 = null;
                }
            }

            DB::beginTransaction();

            $mov = MovimientoSaldo::create([
                'tipo' => "abono",
                'usuario_id' => $id_user,
                'monto' => $montoDecimal,
                'currency' => $paymentIntent->currency ?? env('DIVISA', 'mxn'),
                'amount_cents' => $amount,
                'estatus_movimiento_id' => $estatusPendienteId,
                // no sumamos al saldo_resultante mientras esté pendiente; guardamos saldo_antes para auditoría
                'saldo_antes' => $currentSaldo,
                'saldo_resultante' => $currentSaldo,
                'descripcion' => "Recarga de " . $montoDecimal . " " . ($paymentIntent->currency ?? env('DIVISA', 'mxn')),
                'payment_intent_id' => $paymentIntent->id,
                'stripe_charge_id' => $stripeChargeId,
                'customer_id' => $stripeCustomerId,
                'payment_method' => $stripePaymentMethod,
                'payment_method_type' => $payment_method_type,
                'card_brand' => $card_brand,
                'card_last4' => $card_last4,
                // fees y net se llenan en el webhook cuando tengamos balance_transaction
                'fees_amount' => null,
                'fees_currency' => null,
                'net_amount' => null,
                'fees_raw' => null,
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
            DB::rollBack();
            return ApiResponseHelper::rollback($ex, $ex->getMessage(), 500);
        }
    }

    public function crearPagoByMensual(Request $req)
    {
        try {
            $id_user = $req->input('id_user');
            $idPlan = $req->input('id_plan'); // id del catálogo de montos
            Log::error(env('STRIPE_SECRET'));
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $plan = CatPlanes::find($idPlan);
            if (!$plan) {
                return null;
            }

            $amount = (int) ($plan->precio * 100); // en centavos
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => env('DIVISA'),
                'metadata' => [
                    'user_id' => $id_user,
                    'plan_id' => $plan->id,
                    'origin' => 'web'
                ]
            ]);

            // datos retornados por stripe
            $stripeCustomerId = $paymentIntent->customer ?? null;
            $stripePaymentMethod = $paymentIntent->payment_method ?? null; // puede venir null

            // puede existir un charge ya (no obligatorio)
            $stripeChargeId = null;
            if (!empty($paymentIntent->charges) && !empty($paymentIntent->charges->data) && isset($paymentIntent->charges->data[0]->id)) {
                $stripeChargeId = $paymentIntent->charges->data[0]->id;
            }

            // idempotency key (si el cliente la envió)
            $idempotencyKey = $req->header('Idempotency-Key') ?? $req->header('Idempotency-Key') ?? null;

            // obtener usuario actual (para calcular saldo_antes)
            $user = User::find($id_user);
            //$currentSaldo = $user ? (float) $user->saldo : 0.00;

            // monto decimal en unidad monetaria (positivo = recarga)
            $montoDecimal = $amount / 100;

            // Estatus pendiente = 1 (según tu requerimiento)
            $estatusPendienteId = 1;

            // campos extra a poblar
            $payment_method_type = null;
            $card_brand = null;
            $card_last4 = null;

            // Si viene payment_method (pm_xxx) intentamos recuperarlo para extraer card.brand y last4
            if (!empty($stripePaymentMethod)) {
                try {
                    $pm = PaymentMethod::retrieve($stripePaymentMethod);

                    $payment_method_type = $pm->type ?? null;

                    if (!empty($pm->card)) {
                        $card_brand = $pm->card->brand ?? null;
                        $card_last4 = $pm->card->last4 ?? null;
                    }
                } catch (\Exception $e) {
                    // No hacer fail la creación por esto; solo loguear
                    $payment_method_type = null;
                    $card_brand = null;
                    $card_last4 = null;
                }
            }

            DB::beginTransaction();

            $mov = MovimientoSaldo::create([
                'tipo' => "suscripción",
                'usuario_id' => $id_user,
                'monto' => $montoDecimal,
                'currency' => $paymentIntent->currency ?? env('DIVISA', 'mxn'),
                'amount_cents' => $amount,
                'estatus_movimiento_id' => $estatusPendienteId,
                // no sumamos al saldo_resultante mientras esté pendiente; guardamos saldo_antes para auditoría
                'saldo_antes' => 0,
                'saldo_resultante' => 0,
                'descripcion' => "Suscripción de " . $montoDecimal . " " . ($paymentIntent->currency ?? env('DIVISA', 'mxn')) . " para " . $plan->nombre_plan,
                'payment_intent_id' => $paymentIntent->id,
                'stripe_charge_id' => $stripeChargeId,
                'customer_id' => $stripeCustomerId,
                'payment_method' => $stripePaymentMethod,
                'payment_method_type' => $payment_method_type,
                'card_brand' => $card_brand,
                'card_last4' => $card_last4,
                // fees y net se llenan en el webhook cuando tengamos balance_transaction
                'fees_amount' => null,
                'fees_currency' => null,
                'net_amount' => null,
                'fees_raw' => null,
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

            return ApiResponseHelper::sendResponse($paymentIntent->client_secret, 'Pago creado correctamente para el plan mensual.', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex, $ex->getMessage(), 500);
        }
    }


    public function confirmStripePayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            // opcional: 'requesting_user_id' => 'required|integer' si quieres verificar ownership
        ]);

        $paymentIntentId = $request->input('payment_intent_id');

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Recuperamos PaymentIntent y expandimos charges para tener charge/id
            $pi = PaymentIntent::retrieve($paymentIntentId, ['expand' => ['charges.data.balance_transaction', 'charges.data.payment_method']]);

            if (!$pi || empty($pi->id)) {
                return ApiResponseHelper::rollback(null, 'PaymentIntent no encontrado en Stripe', 404);
            }

            // Buscar movimiento local
            $mov = MovimientoSaldo::where('payment_intent_id', $paymentIntentId)->first();

            if (!$mov) {
                return ApiResponseHelper::rollback(null, 'Movimiento no encontrado en DB', 404);
            }

            // ---- Idempotencia: si ya fue procesado, devolver mensaje claro ----
            if ($mov->processed_at) {
                return ApiResponseHelper::rollback('Movimiento ya procesado', 200); // o 409 según convención
            }

            // Opcional: verificar que el que hace la petición sea el dueño del movimiento
            // if ($request->user()->id !== $mov->usuario_id) { ... }

            // Validación de montos (stripe en centavos)
            $stripeAmount = $pi->amount ?? null;
            $localAmountCents = $mov->amount_cents ?? (int) round($mov->monto * 100);

            if ($stripeAmount !== null && $localAmountCents !== null && (int)$stripeAmount !== (int)$localAmountCents) {
                Log::warning("confirmStripePayment - Mismatch amount: PI {$paymentIntentId} stripe {$stripeAmount} != local {$localAmountCents} (mov_id {$mov->id})");
                // No abortamos, pero queda logueado para auditoría
            }

            $status = $pi->status; // processing | succeeded | canceled | etc

            // intentar extraer charge (si existe)
            $charge = null;
            $stripeChargeId = null;
            if (!empty($pi->latest_charge)) {
                // $charge = $pi->charges->data[0];
                // $stripeChargeId = $pi->latest_charge;
            }

            // Preparar campos que vamos a rellenar
            $payment_method_type = $mov->payment_method_type ?? null;
            $card_brand = $mov->card_brand ?? null;
            $card_last4 = $mov->card_last4 ?? null;
            $fees_amount = $mov->fees_amount ?? null;
            $fees_currency = $mov->fees_currency ?? null;
            $net_amount = $mov->net_amount ?? null;
            $fees_raw = $mov->fees_raw ?? null;

            // Extraer payment method info:
            // 1) PaymentIntent.payment_method
            // 2) Charge.payment_method_details (si el charge existe)
            // 3) Si tenemos pm id en mov->payment_method, intentar PaymentMethod::retrieve
            try {
                // Preferimos la info en charge->payment_method_details.card (si existe)
                if ($charge && !empty($charge->payment_method_details)) {
                    $pmd = $charge->payment_method_details;
                    $payment_method_type = $pmd->type ?? $payment_method_type;

                    if (!empty($pmd->card)) {
                        $card_brand = $pmd->card->brand ?? $card_brand;
                        $card_last4 = $pmd->card->last4 ?? $card_last4;
                    }
                }

                // Si aún no tenemos last4 y tenemos payment_method id en PaymentIntent, intentar retrieve PaymentMethod
                $pmId = $pi->payment_method ?? $mov->payment_method ?? null;
                if (($card_last4 === null || $card_brand === null) && $pmId) {
                    try {
                        $pm = PaymentMethod::retrieve($pmId);
                        $payment_method_type = $pm->type ?? $payment_method_type;
                        if (!empty($pm->card)) {
                            $card_brand = $pm->card->brand ?? $card_brand;
                            $card_last4 = $pm->card->last4 ?? $card_last4;
                        }
                    } catch (\Exception $e) {
                        Log::warning("confirmStripePayment: no se pudo recuperar PaymentMethod {$pmId}: " . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                Log::warning("confirmStripePayment: error extrayendo payment method details: " . $e->getMessage());
            }

            // Extraer balance_transaction / fees desde charge (si existe)
            if ($charge) {
                $balanceTxId = $charge->balance_transaction ?? ($charge->balance_transaction ?? null);
                if ($balanceTxId) {
                    try {
                        // Si ya lo expandimos arriba, podría venir como objeto; manejamos ambos casos
                        $bt = is_string($balanceTxId) ? BalanceTransaction::retrieve($balanceTxId) : $balanceTxId;

                        if ($bt) {
                            // Stripe devuelve en centavos; convertimos a unidad monetaria
                            $fees_amount = isset($bt->fee) ? ($bt->fee / 100) : $fees_amount;
                            $fees_currency = $bt->currency ?? $fees_currency;
                            $net_amount = isset($bt->net) ? ($bt->net / 100) : $net_amount;
                            // Guardamos raw para auditoría (como array)
                            $fees_raw = json_decode(json_encode($bt), true);
                        }
                    } catch (\Exception $e) {
                        Log::warning("confirmStripePayment: no se pudo recuperar BalanceTransaction {$balanceTxId}: " . $e->getMessage());
                    }
                }
            }

            // Empezamos transacción DB
            DB::beginTransaction();

            // Si succeeded -> actualizamos movimiento y saldo
            if ($status === 'succeeded') {
                $estatusCompletado = 2; // Ajusta según tu cat_estatus_movimiento

                // si saldo_antes está vacío, setear a saldo actual del user
                if ($mov->saldo_antes === null) {
                    $userForSaldo = User::find($mov->usuario_id);
                    $mov->saldo_antes = $userForSaldo ? (float) $userForSaldo->saldo : 0.00;
                }

                // Actualizamos campos del movimiento
                $mov->stripe_charge_id = $stripeChargeId ?? $mov->stripe_charge_id;
                $mov->customer_id = $pi->customer ?? $mov->customer_id;
                $mov->payment_method = $pi->payment_method ?? $mov->payment_method;
                $mov->payment_method_type = $payment_method_type;
                $mov->card_brand = $card_brand;
                $mov->card_last4 = $card_last4;
                $mov->currency = $pi->currency ?? $mov->currency;
                $mov->amount_cents = $pi->amount ?? $mov->amount_cents;
                $mov->metadata = isset($pi->metadata) ? json_decode(json_encode($pi->metadata), true) : $mov->metadata;

                // fees
                $mov->fees_amount = $fees_amount;
                $mov->fees_currency = $fees_currency;
                $mov->net_amount = $net_amount;
                $mov->fees_raw = $fees_raw ? json_encode($fees_raw) : $mov->fees_raw;

                $mov->webhook_payload = json_encode($pi); // guardar el PI completo para auditoría
                $mov->processed_at = Carbon::now();
                $mov->failure_code = null;
                $mov->failure_message = null;
                $mov->estatus_movimiento_id = $estatusCompletado;

                // actualizar saldo de usuario y saldo_resultante
                $user = User::find($mov->usuario_id);
                if ($user) {
                    $nuevoSaldo = (float)$user->saldo + ((float)$mov->monto);
                    $user->saldo = $nuevoSaldo;
                    $user->save();

                    $mov->saldo_resultante = $nuevoSaldo;
                } else {
                    // Si no encontramos usuario, dejo saldo_resultante igual a saldo_antes + monto
                    $mov->saldo_resultante = ($mov->saldo_antes ?? 0) + ((float)$mov->monto);
                }

                $mov->save();
                DB::commit();

                return ApiResponseHelper::sendResponse($mov->saldo_resultante, 'Pago confirmado y saldo actualizado.', 200);
            }

            // Si no succeeded: guardar cierta info (charge id, payment_method details), pero no actualizar saldo
            // Esto permite que la UI muestre que está en processing/requires_action, etc.
            $mov->stripe_charge_id = $stripeChargeId ?? $mov->stripe_charge_id;
            $mov->customer_id = $pi->customer ?? $mov->customer_id;
            $mov->payment_method = $pi->payment_method ?? $mov->payment_method;
            $mov->payment_method_type = $payment_method_type;
            $mov->card_brand = $card_brand;
            $mov->card_last4 = $card_last4;
            $mov->amount_cents = $pi->amount ?? $mov->amount_cents;
            $mov->metadata = isset($pi->metadata) ? json_decode(json_encode($pi->metadata), true) : $mov->metadata;
            $mov->webhook_payload = json_encode($pi);
            $mov->save();

            DB::rollBack();
            return ApiResponseHelper::rollback(null, "PaymentIntent no está en estado succeeded (status: {$status})", 400);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error confirmStripePayment: ' . $e->getMessage() . ' - PI:' . ($paymentIntentId ?? 'n/a'));
            return ApiResponseHelper::rollback(null, 'Error al verificar PaymentIntent: ' . $e->getMessage(), 500);
        }
    }


    public function confirmStripePaymentMensual(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            // opcional: 'requesting_user_id' => 'required|integer' si quieres verificar ownership
        ]);

        $paymentIntentId = $request->input('payment_intent_id');

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Recuperamos PaymentIntent y expandimos charges para tener charge/id
            $pi = PaymentIntent::retrieve($paymentIntentId, ['expand' => ['charges.data.balance_transaction', 'charges.data.payment_method']]);

            if (!$pi || empty($pi->id)) {
                return ApiResponseHelper::rollback(null, 'PaymentIntent no encontrado en Stripe', 404);
            }

            // Buscar movimiento local
            $mov = MovimientoSaldo::where('payment_intent_id', $paymentIntentId)->first();

            if (!$mov) {
                return ApiResponseHelper::rollback(null, 'Movimiento no encontrado en DB', 404);
            }

            // ---- Idempotencia: si ya fue procesado, devolver mensaje claro ----
            if ($mov->processed_at) {
                return ApiResponseHelper::rollback('Movimiento ya procesado', 200); // o 409 según convención
            }

            // Opcional: verificar que el que hace la petición sea el dueño del movimiento
            // if ($request->user()->id !== $mov->usuario_id) { ... }

            // Validación de montos (stripe en centavos)
            $stripeAmount = $pi->amount ?? null;
            $localAmountCents = $mov->amount_cents ?? (int) round($mov->monto * 100);

            if ($stripeAmount !== null && $localAmountCents !== null && (int)$stripeAmount !== (int)$localAmountCents) {
                Log::warning("confirmStripePayment - Mismatch amount: PI {$paymentIntentId} stripe {$stripeAmount} != local {$localAmountCents} (mov_id {$mov->id})");
                // No abortamos, pero queda logueado para auditoría
            }

            $status = $pi->status; // processing | succeeded | canceled | etc

            // intentar extraer charge (si existe)
            $charge = null;
            $stripeChargeId = null;
            if (!empty($pi->latest_charge)) {
                // $charge = $pi->charges->data[0];
                // $stripeChargeId = $pi->latest_charge;
            }

            // Preparar campos que vamos a rellenar
            $payment_method_type = $mov->payment_method_type ?? null;
            $card_brand = $mov->card_brand ?? null;
            $card_last4 = $mov->card_last4 ?? null;
            $fees_amount = $mov->fees_amount ?? null;
            $fees_currency = $mov->fees_currency ?? null;
            $net_amount = $mov->net_amount ?? null;
            $fees_raw = $mov->fees_raw ?? null;

            // Extraer payment method info:
            // 1) PaymentIntent.payment_method
            // 2) Charge.payment_method_details (si el charge existe)
            // 3) Si tenemos pm id en mov->payment_method, intentar PaymentMethod::retrieve
            try {
                // Preferimos la info en charge->payment_method_details.card (si existe)
                if ($charge && !empty($charge->payment_method_details)) {
                    $pmd = $charge->payment_method_details;
                    $payment_method_type = $pmd->type ?? $payment_method_type;

                    if (!empty($pmd->card)) {
                        $card_brand = $pmd->card->brand ?? $card_brand;
                        $card_last4 = $pmd->card->last4 ?? $card_last4;
                    }
                }

                // Si aún no tenemos last4 y tenemos payment_method id en PaymentIntent, intentar retrieve PaymentMethod
                $pmId = $pi->payment_method ?? $mov->payment_method ?? null;
                if (($card_last4 === null || $card_brand === null) && $pmId) {
                    try {
                        $pm = PaymentMethod::retrieve($pmId);
                        $payment_method_type = $pm->type ?? $payment_method_type;
                        if (!empty($pm->card)) {
                            $card_brand = $pm->card->brand ?? $card_brand;
                            $card_last4 = $pm->card->last4 ?? $card_last4;
                        }
                    } catch (\Exception $e) {
                        Log::warning("confirmStripePayment: no se pudo recuperar PaymentMethod {$pmId}: " . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                Log::warning("confirmStripePayment: error extrayendo payment method details: " . $e->getMessage());
            }

            // Extraer balance_transaction / fees desde charge (si existe)
            if ($charge) {
                $balanceTxId = $charge->balance_transaction ?? ($charge->balance_transaction ?? null);
                if ($balanceTxId) {
                    try {
                        // Si ya lo expandimos arriba, podría venir como objeto; manejamos ambos casos
                        $bt = is_string($balanceTxId) ? BalanceTransaction::retrieve($balanceTxId) : $balanceTxId;

                        if ($bt) {
                            // Stripe devuelve en centavos; convertimos a unidad monetaria
                            $fees_amount = isset($bt->fee) ? ($bt->fee / 100) : $fees_amount;
                            $fees_currency = $bt->currency ?? $fees_currency;
                            $net_amount = isset($bt->net) ? ($bt->net / 100) : $net_amount;
                            // Guardamos raw para auditoría (como array)
                            $fees_raw = json_decode(json_encode($bt), true);
                        }
                    } catch (\Exception $e) {
                        Log::warning("confirmStripePayment: no se pudo recuperar BalanceTransaction {$balanceTxId}: " . $e->getMessage());
                    }
                }
            }

            // Empezamos transacción DB
            DB::beginTransaction();

            // Si succeeded -> actualizamos movimiento y saldo
            if ($status === 'succeeded') {
                $estatusCompletado = 2; // Ajusta según tu cat_estatus_movimiento


                // Actualizamos campos del movimiento
                $mov->stripe_charge_id = $stripeChargeId ?? $mov->stripe_charge_id;
                $mov->customer_id = $pi->customer ?? $mov->customer_id;
                $mov->payment_method = $pi->payment_method ?? $mov->payment_method;
                $mov->payment_method_type = $payment_method_type;
                $mov->card_brand = $card_brand;
                $mov->card_last4 = $card_last4;
                $mov->currency = $pi->currency ?? $mov->currency;
                $mov->amount_cents = $pi->amount ?? $mov->amount_cents;
                $mov->metadata = isset($pi->metadata) ? json_decode(json_encode($pi->metadata), true) : $mov->metadata;

                // fees
                $mov->fees_amount = $fees_amount;
                $mov->fees_currency = $fees_currency;
                $mov->net_amount = $net_amount;
                $mov->fees_raw = $fees_raw ? json_encode($fees_raw) : $mov->fees_raw;

                $mov->webhook_payload = json_encode($pi); // guardar el PI completo para auditoría
                $mov->processed_at = Carbon::now();
                $mov->failure_code = null;
                $mov->failure_message = null;
                $mov->estatus_movimiento_id = $estatusCompletado;

                // actualizar saldo de usuario y saldo_resultante
                $user = User::find($mov->usuario_id);
                $metadata = $pi->metadata;

                // Acceder directamente al plan_id y user_id
                $planId = $metadata->plan_id;
                $sus = Suscripciones::create([
                    'usuario_id' => $user->id,
                    'id_plan' => $planId,
                    'fecha_inicio' => now(),
                    'fecha_vencimiento' => now()->addMonth(), // ejemplo: 1 mes
                    'estado' => Suscripciones::ESTADO_ACTIVA,
                    'perfiles_utilizados' => 0,
                    'facturas_realizadas' => 0,
                ]);

                $mov->save();
                DB::commit();

                return ApiResponseHelper::sendResponse($mov->saldo_resultante, 'Pago confirmado y saldo actualizado.', 200);
            }

            // Si no succeeded: guardar cierta info (charge id, payment_method details), pero no actualizar saldo
            // Esto permite que la UI muestre que está en processing/requires_action, etc.
            $mov->stripe_charge_id = $stripeChargeId ?? $mov->stripe_charge_id;
            $mov->customer_id = $pi->customer ?? $mov->customer_id;
            $mov->payment_method = $pi->payment_method ?? $mov->payment_method;
            $mov->payment_method_type = $payment_method_type;
            $mov->card_brand = $card_brand;
            $mov->card_last4 = $card_last4;
            $mov->amount_cents = $pi->amount ?? $mov->amount_cents;
            $mov->metadata = isset($pi->metadata) ? json_decode(json_encode($pi->metadata), true) : $mov->metadata;
            $mov->webhook_payload = json_encode($pi);
            $mov->save();

            DB::rollBack();
            return ApiResponseHelper::rollback(null, "PaymentIntent no está en estado succeeded (status: {$status})", 400);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error confirmStripePayment: ' . $e->getMessage() . ' - PI:' . ($paymentIntentId ?? 'n/a'));
            return ApiResponseHelper::rollback(null, 'Error al verificar PaymentIntent: ' . $e->getMessage(), 500);
        }
    }
}
