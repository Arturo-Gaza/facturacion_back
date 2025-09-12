<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\FacturaRepositoryInterface;
use App\Models\Factura;

class FacturaRepository implements FacturaRepositoryInterface
{
    public function getAll()
    {
        return Factura::with(['solicitud', 'empleado', 'servicio'])->get();
    }

    public function getByID($id): ?Factura
    {
        return Factura::with(['solicitud', 'empleado', 'servicio'])->find($id);
    }

    public function store(array $data): Factura
    {
        return Factura::create($data);
    }

    public function update(array $data, $id): ?Factura
    {
        $factura = Factura::find($id);
        if ($factura) {
            $factura->update($data);
        }
        return $factura;
    }
}