<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\TabDireccionesRepositoryInterface;
use App\Models\sistemaFacturacion\TabDirecciones;

class TabDireccionesRepository implements TabDireccionesRepositoryInterface
{
    public function getAll()
    {
        return TabDirecciones::with(['cliente', 'tipoDireccion'])->get();
    }

    public function getByID($id): ?TabDirecciones
    {
        return TabDirecciones::with(['cliente', 'tipoDireccion'])
            ->where('id_direccion', $id)
            ->first();
    }

    public function store(array $data): TabDirecciones
    {
        return TabDirecciones::create($data);
    }

    public function update(array $data, $id): ?TabDirecciones
    {
        $direccion = TabDirecciones::where('id_direccion', $id)->first();
        if ($direccion) {
            $direccion->update($data);
        }
        return $direccion;
    }
}
