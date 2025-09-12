<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\MovimientoSaldo;

interface MovimientoSaldoRepositoryInterface
{
    public function getAll();
    public function getByID($id): ?MovimientoSaldo;
    public function store(array $data): MovimientoSaldo;
    public function update(array $data, $id): ?MovimientoSaldo;
}