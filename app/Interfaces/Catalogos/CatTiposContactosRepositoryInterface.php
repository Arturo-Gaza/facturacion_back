<?php

namespace App\Interfaces\Catalogos;

use App\Models\Catalogos\CatTiposContacto;

interface CatTiposContactosRepositoryInterface
{
    //
    public function getAll();
    public function getByID($id): ?CatTiposContacto;
    public function store(array $data): CatTiposContacto;
    public function update(array $data, $id): ?CatTiposContacto;
}
