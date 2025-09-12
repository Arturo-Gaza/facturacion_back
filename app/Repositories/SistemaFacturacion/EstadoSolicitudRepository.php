<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\EstadoSolicitudRepositoryInterface;
use App\Models\EstadoSolicitud;

class EstadoSolicitudRepository implements EstadoSolicitudRepositoryInterface
{
    public function getAll()
    {
        return EstadoSolicitud::all();
    }

    public function getByID($id): ?EstadoSolicitud
    {
        return EstadoSolicitud::find($id);
    }

    public function store(array $data): EstadoSolicitud
    {
        return EstadoSolicitud::create($data);
    }

    public function update(array $data, $id): ?EstadoSolicitud
    {
        $estado = EstadoSolicitud::find($id);
        if ($estado) {
            $estado->update($data);
        }
        return $estado;
    }
}