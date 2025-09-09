<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\sistemaFacturacion\TabContactos;

interface TabContactosRepositoryInterface
{
    public function getAll();
    public function getByID($id): ?TabContactos;
    public function store(array $data): TabContactos;
    public function update(array $data, $id): ?TabContactos;
}
