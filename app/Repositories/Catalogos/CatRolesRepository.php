<?php

namespace App\Repositories\Catalogos;

use App\Interfaces\Catalogos\CatRolesRepositoryInterface;
use App\Models\Catalogos\CatRoles;

class CatRolesRepository implements CatRolesRepositoryInterface
{
    public function getAll()
    {
        return CatRoles::all();
    }

    public function getByID($id): ?CatRoles
    {
        return CatRoles::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return CatRoles::create($data);
    }

    public function update(array $data, $id)
    {
        return CatRoles::where('id',$id)->update($data);
    }
}
