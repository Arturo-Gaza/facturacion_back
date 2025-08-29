<?php

namespace App\Repositories\SistemaTickets;

use App\Interfaces\SistemaTickets\TabArchivosObservacionesSolicitudReqInfoRepositoryInterface;
use App\Models\SistemaTickets\TabArchivosObservacionesSolicitudReqInfo;


class TabArchivosObservacionesSolicitudReqInfoRepository implements TabArchivosObservacionesSolicitudReqInfoRepositoryInterface
{
    public function getAll()
    {
        return TabArchivosObservacionesSolicitudReqInfo::all();
    }

    public function getByID($id): ?TabArchivosObservacionesSolicitudReqInfo
    {

        $registro = TabArchivosObservacionesSolicitudReqInfo::where('id', $id)->first();

        if ($registro) {
            $registro->makeVisible('archivo');
        }
        return $registro;
    }

    public function store(array $data)
    {
        return TabArchivosObservacionesSolicitudReqInfo::create($data);
    }

    public function update(array $data, $id)
    {
        return TabArchivosObservacionesSolicitudReqInfo::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return TabArchivosObservacionesSolicitudReqInfo::destroy($id);
    }
}
