<?php

namespace App\Repositories\Catalogos;

use App\Interfaces\Catalogos\CatUnidadMedidasRepositoryInterface;
use App\Models\Catalogos\CatUnidadMedida;

class CatUnidadMedidasRepository implements CatUnidadMedidasRepositoryInterface
{
    public function getAll()
    {
        return CatUnidadMedida::all();
    }

    public function getByID($id): ?CatUnidadMedida
    {
        return CatUnidadMedida::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return CatUnidadMedida::create($data);
    }

    public function update(array $data, $id)
    {
        return CatUnidadMedida::where('id',$id)->update($data);
    }
}
