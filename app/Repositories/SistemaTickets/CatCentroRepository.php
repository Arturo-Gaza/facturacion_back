<?php

namespace App\Repositories\SistemaTickets;

use App\Interfaces\SistemaTickets\CatCentroRepositoryInterface;
use App\Models\SistemaTickets\CatCentro;

class CatCentroRepository implements CatCentroRepositoryInterface
{
    public function getAll()
    {
        return CatCentro::all();
    }

    public function getByID($id): ?CatCentro
    {
        return CatCentro::where('id_centro', $id)->first();
    }

    public function store(array $data)
    {
        return CatCentro::create($data);
    }

    public function update(array $data, $id)
    {
        return CatCentro::where('id_centro',$id)->update($data);
    }
}
