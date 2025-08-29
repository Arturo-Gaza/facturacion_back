<?php

namespace App\Repositories\SistemaTickets;

use App\Interfaces\ArchivoCarga\TabObservacionesRepositoryInterface;
use App\Interfaces\SistemaTickets\TabObesrvacionesDetalleRepositoryInterface;
use App\Models\SistemaTickets\TabObservacionesSolicitudesDetalle;

class TabObesrvacionesDetalleRepository implements TabObesrvacionesDetalleRepositoryInterface
{
    public function getAll()
    {
        return TabObservacionesSolicitudesDetalle::all();
    }

    public function getByID($id): ?TabObservacionesSolicitudesDetalle
    {
        return TabObservacionesSolicitudesDetalle::with('archivos')->where('id', $id)->first();
    }
        public function getByIdDetalle($id)
    {
        return TabObservacionesSolicitudesDetalle::with('archivos')->where('id_solicitud_detalle', $id)->get();
    }

    public function store(array $data)
    {
        return TabObservacionesSolicitudesDetalle::create($data);
    }

    public function update(array $data, $id)
    {
        return TabObservacionesSolicitudesDetalle::where('id', $id)->update($data);
    }
}
