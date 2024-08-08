<?php

namespace App\Repositories\Catalogos;

use App\Interfaces\Catalogos\CatGpoFamiliaRepositoryInterface;
use App\Models\Catalogos\CatGpoFamilia;

class CatGpoFamiliaRepository implements CatGpoFamiliaRepositoryInterface
{
    
    public function getAll()
    {
        return CatGpoFamilia::all();
    }

    public function getByID($id): ?CatGpoFamilia
    {
        return CatGpoFamilia::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return CatGpoFamilia::create($data);
    }

    public function update(array $data, $id)
    {
        return CatGpoFamilia::where('id',$id)->update($data);
    }
}
