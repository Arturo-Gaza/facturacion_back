<?php

namespace App\Repositories\SistemaTickets;

use App\Interfaces\SistemaTickets\TabArchivosObservacionesDetalleRepositoryInterface;
use App\Models\SistemaTickets\TabArchivosObservacionesDetalle;


class TabArchivosObservacionesDetalleRepository implements TabArchivosObservacionesDetalleRepositoryInterface
{
    public function getAll()
    {
        return TabArchivosObservacionesDetalle::all();
    }

    public function getByID($id): ?TabArchivosObservacionesDetalle
    {

        $registro = TabArchivosObservacionesDetalle::where('id', $id)->first();

        if ($registro) {
            $registro->makeVisible('archivo');
        }
        return $registro;
    }

    public function store(array $data)
    {
        return TabArchivosObservacionesDetalle::create($data);
    }

    public function update(array $data, $id)
    {
        return TabArchivosObservacionesDetalle::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return TabArchivosObservacionesDetalle::destroy($id);
    }
}
