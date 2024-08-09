<?php

namespace App\Repositories\Catalogos;

use App\Interfaces\Catalogos\CatProductosRepositoryInterface;
use App\Models\Catalogos\CatProductos;

class CatProductosRepository implements CatProductosRepositoryInterface
{
    public function getAll()
    {
        return CatProductos::all();
    }

    public function getByID($id): ?CatProductos
    {
        return CatProductos::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return CatProductos::create($data);
    }

    public function update(array $data, $id)
    {
        return CatProductos::where('id',$id)->update($data);
    }
}
