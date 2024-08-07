<?php

namespace App\Interfaces\Catalogos;

interface CatRolesRepositoryInterface
{
    //
    public function getAll();
    public function getByID($id);
    public function store(array $data);
    public function update(array $data, $id);
}
