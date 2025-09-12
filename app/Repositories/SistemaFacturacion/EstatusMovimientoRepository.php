<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\EstatusMovimientoRepositoryInterface;
use App\Models\EstatusMovimiento;

class EstatusMovimientoRepository implements EstatusMovimientoRepositoryInterface
{
    public function getAll()
    {
        return EstatusMovimiento::all();
    }

    public function getByID($id): ?EstatusMovimiento
    {
        return EstatusMovimiento::find($id);
    }

    public function store(array $data): EstatusMovimiento
    {
        return EstatusMovimiento::create($data);
    }

    public function update(array $data, $id): ?EstatusMovimiento
    {
        $estatus = EstatusMovimiento::find($id);
        if ($estatus) {
            $estatus->update($data);
        }
        return $estatus;
    }
}