<?php

namespace App\Repositories\Catalogos;

use App\Interfaces\Catalogos\CatEstatusSatRepositoryInterface;
use App\Models\Catalogos\CatEstatusesSat;

class CatEstatusSatRepository implements CatEstatusSatRepositoryInterface
{
    public function getAll()
    {
        return CatEstatusesSat::all();
    }

    public function getByID($id): ?CatEstatusesSat
    {
        return CatEstatusesSat::where('id_estatus_sat', $id)->first();
    }

    public function store(array $data): CatEstatusesSat
    {
        return CatEstatusesSat::create($data);
    }

    public function update(array $data, $id): ?CatEstatusesSat
    {
        $estatus = CatEstatusesSat::where('id_estatus_sat', $id)->first();
        if ($estatus) {
            $estatus->update($data);
        }
        return $estatus;
    }
}
