<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\EstadoSolicitud;

interface EstadoSolicitudRepositoryInterface
{
    public function getAll();
    public function getByID($id): ?EstadoSolicitud;
    public function store(array $data): EstadoSolicitud;
    public function update(array $data, $id): ?EstadoSolicitud;
}