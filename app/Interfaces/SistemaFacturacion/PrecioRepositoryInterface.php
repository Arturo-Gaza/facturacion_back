<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\Precio;

interface PrecioRepositoryInterface
{
    public function getAll();
    public function getByID($id): ?Precio;
    public function store(array $data): Precio;
    public function update(array $data, $id): ?Precio;
}
