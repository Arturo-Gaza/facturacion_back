<?php

namespace App\Interfaces\SistemaFacturacion;

interface TabClientesRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function store(array $data);
    public function update(array $data, $id);
}
