<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\TabContactosRepositoryInterface;
use App\Models\sistemaFacturacion\TabContactos;

class TabContactoRepository implements TabContactosRepositoryInterface
{
    public function getAll()
    {
        return TabContactos::with(['cliente', 'tipoContacto'])->get();
    }

    public function getByID($id): ?TabContactos
    {
        return TabContactos::with(['cliente', 'tipoContacto'])
            ->where('id_contacto', $id)
            ->first();
    }

    public function store(array $data): TabContactos
    {
        return TabContactos::create($data);
    }

    public function update(array $data, $id): ?TabContactos
    {
        $contacto = TabContactos::where('id_contacto', $id)->first();
        if ($contacto) {
            $contacto->update($data);
        }
        return $contacto;
    }
}
