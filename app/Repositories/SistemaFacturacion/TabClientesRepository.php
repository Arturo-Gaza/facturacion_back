<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\TabClientesRepositoryInterface;
use App\Models\sistemaFacturacion\TabClientes;

class TabClientesRepository implements TabClientesRepositoryInterface
{
    public function getAll()
    {
        return TabClientes::all();
    }

    public function getByID($id): ?TabClientes
    {
        return TabClientes::where('id_cliente', $id)->first();
    }

    public function store(array $data): TabClientes
    {
        return TabClientes::create($data);
    }

    public function update(array $data, $id): ?TabClientes
    {
        $cliente = TabClientes::where('id_cliente', $id)->first();
        if ($cliente) {
            $cliente->update($data);
        }
        return $cliente;
    }
}
