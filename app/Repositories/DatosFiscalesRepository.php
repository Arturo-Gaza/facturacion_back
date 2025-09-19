<?php

namespace App\Repositories;

use App\Interfaces\DatosFiscalesRepositoryInterface;
use App\Models\DatosFiscal;
use App\Models\Direccion;

class DatosFiscalesRepository implements DatosFiscalesRepositoryInterface
{
    public function getAll()
    {
        return DatosFiscal::with('direcciones')->get();
    }

    public function getByID($id): ?DatosFiscal
    {
        return DatosFiscal::with('direcciones')->find($id);
    }

    public function storeConDomicilio(array $data, array $direccion )
    {
        $datosFiscales = DatosFiscal::create($data);
        
        if ($direccion && $datosFiscales) {
            $direccion['id_fiscal'] = $datosFiscales->id;
            Direccion::create($direccion);
        }
        
        return $datosFiscales->load('direcciones');
    }
        public function store(array $data): DatosFiscal
    {
        return DatosFiscal::create($data);
    }

    public function update(array $data, $id): ?DatosFiscal
    {
        $datosFiscales = DatosFiscal::find($id);
        if ($datosFiscales) {
            $datosFiscales->update($data);
        }
        return $datosFiscales;
    }
}