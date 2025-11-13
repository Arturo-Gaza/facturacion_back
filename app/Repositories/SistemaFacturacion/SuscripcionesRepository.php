<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\SuscripcionesRepositoryInterface;
use App\Models\CatPlanes;
use App\Models\Suscripciones;
use Exception;
use Illuminate\Support\Carbon;
use Stripe\Stripe;
use Illuminate\Support\Str;
use Stripe\Checkout\Session;

class SuscripcionesRepository implements SuscripcionesRepositoryInterface
{


    public function getAll()
    {
        return Suscripciones::get();
    }
    public function iniciar($id_user, $id_plan)
    {


        $plan = CatPlanes::findOrFail($id_plan);
        $exist = Suscripciones::where('usuario_id', $id_user)
            ->where('id_plan', $plan->id)
            ->where('estado', Suscripciones::ESTADO_ACTIVA)
            ->first();
        if ($exist) {
            throw new Exception("Ya existe una suscripción activa para este plan");
        }
        $precio = $plan->precio ?? 0;
        $esMensual = $plan->esMensual();
        $tipoPago = $plan->tipo_pago ?? null;
        if ($precio == 0 || $plan->esMensual()) {
            $dias_gratis = $plan->dias_gratis;
            $vigencia_fin = $plan->esMensual() ? Carbon::now()->addDay($dias_gratis) : null;

            $sus = Suscripciones::create([
                'usuario_id' => $id_user,
                'id_plan' => $plan->id,
                'fecha_inicio' => now(),
                'fecha_vencimiento' => $vigencia_fin, // ejemplo: 1 mes
                'estado' => Suscripciones::ESTADO_ACTIVA,
                'perfiles_utilizados' => 0,
                'facturas_realizadas' => 0,
            ]);

            return [
                'suscripcion' => $sus,
                'tipo_pago' => $tipoPago,
            ];
        }
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $successUrl = route('suscripciones.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl  = route('suscripciones.checkout.cancel');
        $metadata = [
            'usuario_id' => (string)$id_user,
            'plan_id' => (string)$plan->id,
            'internal_id' => Str::uuid()->toString(),
            'tipo_pago' => $tipoPago,
        ];
        $session = Session::create([
            'mode' => 'payment',
            'automatic_payment_methods' => ['enabled' => true],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'mxn', // ajusta según tu moneda
                    'product_data' => [
                        'name' => $plan->nombre_plan ?? $plan->nombre ?? 'Plan',
                    ],
                    'unit_amount' => intval(round($precio * 100)), // en centavos
                ],
                'quantity' => 1,
            ]],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => $metadata,
        ]);

        // Opcional: crear registro "pendiente" para evitar duplicados (stripe_session_id)
        $susPendiente = Suscripciones::create([
            'usuario_id' => $id_user,
            'id_plan' => $plan->id,
            'fecha_inicio' => now(),
            'fecha_vencimiento' => Carbon::now()->addMonth(),
            'estado' => 'pendiente',
            'perfiles_utilizados' => 0,
            'facturas_realizadas' => 0,
            'stripe_session_id' => $session->id,
        ]);
        return [
            'suscripcion' => $susPendiente,
            'tipo_pago' => $tipoPago,
        ];
    }
}
