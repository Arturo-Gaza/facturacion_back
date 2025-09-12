<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\EstatusMovimiento;

interface EstatusMovimientoRepositoryInterface
{
    public function getAll();
    public function getByID($id): ?EstatusMovimiento;
    public function store(array $data): EstatusMovimiento;
    public function update(array $data, $id): ?EstatusMovimiento;
}