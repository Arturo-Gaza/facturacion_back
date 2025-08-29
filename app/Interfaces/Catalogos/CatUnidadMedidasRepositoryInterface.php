<?php

namespace App\Interfaces\Catalogos;

interface CatUnidadMedidasRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function store(array $data);
    public function update(array $data, $id);
    public function exportar( $data);
}
