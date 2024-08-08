<?php

namespace App\Repositories\Catalogos;
use App\Models\Catalogos\CatAlmacenes;
use App\Interfaces\Catalogos\CatAlmacenesRepositoryInterface;

class CatAlmacenesRepository implements CatAlmacenesRepositoryInterface
{
    public function getAll()
    {
        return CatAlmacenes::all();
    }

    public function getByID($id): ?CatAlmacenes
    {
        return CatAlmacenes::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return CatAlmacenes::create($data);
    }

    public function update(array $data, $id)
    {
        return CatAlmacenes::where('id',$id)->update($data);
    }
}
