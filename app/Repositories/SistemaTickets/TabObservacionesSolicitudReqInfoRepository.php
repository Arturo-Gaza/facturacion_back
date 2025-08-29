<?php

namespace App\Repositories\SistemaTickets;

use App\Interfaces\SistemaTickets\TabObservacionesSolicitudReqInfoRepositoryInterface;
use App\Models\SistemaTickets\TabObservacionesSolicitudReqInfo;

class TabObservacionesSolicitudReqInfoRepository implements TabObservacionesSolicitudReqInfoRepositoryInterface
{
    public function getAll()
    {
        return TabObservacionesSolicitudReqInfo::all();
    }

    public function getByID($id): ?TabObservacionesSolicitudReqInfo
    {
        return TabObservacionesSolicitudReqInfo::with('archivos')->where('id', $id)->first();

    }
        public function getByIdSolicitud($id)
    {
        return TabObservacionesSolicitudReqInfo::with('archivos')->where('id_solicitud_detalle', $id)->get();


    }

    public function store(array $data)
    {
        return TabObservacionesSolicitudReqInfo::create($data);
    }

    public function update(array $data, $id)
    {
        return TabObservacionesSolicitudReqInfo::where('id', $id)->update($data);
    }
}
