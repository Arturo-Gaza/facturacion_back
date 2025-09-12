<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\ServicioRepositoryInterface;
use App\Models\Servicio;

class ServicioRepository implements ServicioRepositoryInterface
{
    public function getAll()
    {
        return Servicio::all();
    }

    public function getByID($id): ?Servicio
    {
        return Servicio::find($id);
    }

    public function store(array $data): Servicio
    {
        return Servicio::create($data);
    }

    public function update(array $data, $id): ?Servicio
    {
        $servicio = Servicio::find($id);
        if ($servicio) {
            $servicio->update($data);
        }
        return $servicio;
    }
}