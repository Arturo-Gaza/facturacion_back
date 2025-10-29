<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\MovimientoSaldoRepositoryInterface;
use App\Models\MovimientoSaldo;
use Stripe\PaymentMethod;
use Stripe\Stripe;
use Stripe\BalanceTransaction;
use Stripe\PaymentIntent;

class MovimientoSaldoRepository implements MovimientoSaldoRepositoryInterface
{
    public function getAll()
    {
        return MovimientoSaldo::get();
    }

    public function getByID($id): ?MovimientoSaldo
    {
        return MovimientoSaldo::find($id);
    }

    public function getMyMovimientos($idUsr)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $movimientosCollection = MovimientoSaldo::where('usuario_id', $idUsr)
            ->with(['estatusMovimiento' => function ($query) {
                $query->select('id', 'nombre');
            }])
            ->get();



        $result = $movimientosCollection->map(function ($movimiento) use (&$paymentMethodCache, &$intentCache, &$balanceTxCache) {
            // base
            $item = [
                'id' => $movimiento->id,
                'monto' => $movimiento->monto,
                'currency' => $movimiento->currency,
                'descripcion' => $movimiento->descripcion,
                'saldo_antes' => $movimiento->saldo_antes,
                'saldo_resultante' => $movimiento->saldo_resultante ,
                'tipo' => $movimiento->tipo,
                'estatus' => $movimiento->estatusMovimiento->nombre ?? null,
                'payment_method' => $movimiento->payment_method,
                'fecha_creacion' => $movimiento->created_at ? $movimiento->created_at->format('Y-m-d H:i:s') : null,
                'fecha_procesado' => $movimiento->processed_at ? $movimiento->processed_at->format('Y-m-d H:i:s') : null,
              
                'tarjeta' => $movimiento->card_last4,
                'card_brand' => $movimiento->card_brand,
                'payment_method_type' => $movimiento->payment_method_type,
            ];

            return $item;
        });

        return $result;
    }

    public function store(array $data): MovimientoSaldo
    {
        return MovimientoSaldo::create($data);
    }

    public function update(array $data, $id): ?MovimientoSaldo
    {
        $movimiento = MovimientoSaldo::find($id);
        if ($movimiento) {
            $movimiento->update($data);
        }
        return $movimiento;
    }
}
