<?php

namespace App\Interfaces\Catalogos;

interface CatUbicacionesRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function store(array $data);
    public function update(array $data, $id);
}
