<?php

namespace App\Interfaces\SistemaFacturacion;

interface CatPlanesRepositoryInterface
{
    public function getAll();
    public function getAllVigentes();
    public function getById($id);
    public function store(array $data);
    public function update(array $data, $id);
}