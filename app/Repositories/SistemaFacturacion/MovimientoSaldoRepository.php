<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\MovimientoSaldoRepositoryInterface;
use App\Models\MovimientoSaldo;

class MovimientoSaldoRepository implements MovimientoSaldoRepositoryInterface
{
    public function getAll()
    {
        return MovimientoSaldo::with(['usuario', 'tipoMovimiento', 'factura'])->get();
    }

    public function getByID($id): ?MovimientoSaldo
    {
        return MovimientoSaldo::with(['usuario', 'tipoMovimiento', 'factura'])->find($id);
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