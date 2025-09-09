<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\TabClientesFiscalesRepositoryInterface;
use App\Models\sistemaFacturacion\TabClientesFiscales;

class TabClientesFiscalesRepository implements TabClientesFiscalesRepositoryInterface
{
    public function getAll()
    {
        return TabClientesFiscales::with(['cliente', 'regimen', 'estatusSat'])->get();
    }

    public function getByID($id): ?TabClientesFiscales
    {
        return TabClientesFiscales::with(['cliente', 'regimen', 'estatusSat'])
            ->where('id_fiscal', $id)
            ->first();
    }

    public function store(array $data): TabClientesFiscales
    {
        return TabClientesFiscales::create($data);
    }

    public function update(array $data, $id): ?TabClientesFiscales
    {
        $fiscal = TabClientesFiscales::where('id_fiscal', $id)->first();
        if ($fiscal) {
            $fiscal->update($data);
        }
        return $fiscal;
    }
}
