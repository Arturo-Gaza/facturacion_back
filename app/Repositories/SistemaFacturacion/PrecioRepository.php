<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\PrecioRepositoryInterface;
use App\Models\Precio;

class PrecioRepository implements PrecioRepositoryInterface
{
    public function getAll()
    {
        return Precio::with('servicio')->get();
    }

    public function getByID($id): ?Precio
    {
        return Precio::with('servicio')->find($id);
    }

    public function store(array $data): Precio
    {
        return Precio::create($data);
    }

    public function update(array $data, $id): ?Precio
    {
        $precio = Precio::find($id);
        if ($precio) {
            $precio->update($data);
        }
        return $precio;
    }
}
