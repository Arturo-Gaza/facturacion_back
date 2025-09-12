<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\TabClientesFiscales;

interface TabClientesFiscalesRepositoryInterface
{
    public function getAll();
    public function getByID($id): ?TabClientesFiscales;
    public function store(array $data): TabClientesFiscales;
    public function update(array $data, $id): ?TabClientesFiscales;
}
