<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\Servicio;

interface ServicioRepositoryInterface
{
    public function getAll();
    public function getByID($id): ?Servicio;
    public function store(array $data): Servicio;
    public function update(array $data, $id): ?Servicio;
}