<?php

namespace App\Interfaces\Catalogos;

use App\Models\Catalogos\CatTiposDireccion;

interface CatTipoDireccionRepositoryInterface
{
    public function getAll();
    public function getByID($id): ?CatTiposDireccion;
    public function store(array $data): CatTiposDireccion;
    public function update(array $data, $id): ?CatTiposDireccion;
}
