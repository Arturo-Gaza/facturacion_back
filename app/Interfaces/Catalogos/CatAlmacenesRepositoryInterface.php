<?php

namespace App\Interfaces\Catalogos;

interface CatAlmacenesRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function store(array $data);
    public function update(array $data, $id);
}
