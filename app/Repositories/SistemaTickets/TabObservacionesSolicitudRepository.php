<?php

namespace App\Repositories\SistemaTickets;

use App\Interfaces\SistemaTickets\TabObesrvacionesSolicitudRepositoryInterface;
use App\Models\SistemaTickets\TabObservacionesSolicitudes;
use Illuminate\Support\Collection;

class TabObservacionesSolicitudRepository implements TabObesrvacionesSolicitudRepositoryInterface
{
    public function getAll()
    {
        return TabObservacionesSolicitudes::all();
    }

    public function getByID($id): ?TabObservacionesSolicitudes
    {
        return TabObservacionesSolicitudes::where('id', $id)->first();
    }

    public function getBySolicitudID($idSolicitud): Collection
    {
        return TabObservacionesSolicitudes::with([
            'usuario' => function ($q) {
                $q->select('id', 'name', 'apellidoP', 'apellidoM', 'idRol');
            },
            'usuario.rol'
        ])
            ->where('id_solicitud', $idSolicitud)
            ->get();
    }

    public function store(array $data)
    {
        return TabObservacionesSolicitudes::create($data);
    }

    public function update(array $data, $id)
    {
        return TabObservacionesSolicitudes::where('id', $id)->update($data);
    }
}
