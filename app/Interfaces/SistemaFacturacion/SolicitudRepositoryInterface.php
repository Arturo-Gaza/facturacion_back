<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\Solicitud;

interface SolicitudRepositoryInterface
{
    public function getAll();
    public function getByID($id): ?Solicitud;
    public function store(array $data): Solicitud;
    public function update(array $data, $id): ?Solicitud;
}