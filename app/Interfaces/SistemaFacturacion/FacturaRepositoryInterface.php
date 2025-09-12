<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\Factura;

interface FacturaRepositoryInterface
{
    public function getAll();
    public function getByID($id): ?Factura;
    public function store(array $data): Factura;
    public function update(array $data, $id): ?Factura;
}