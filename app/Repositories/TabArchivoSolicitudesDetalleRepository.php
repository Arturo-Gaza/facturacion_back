<?php

namespace App\Repositories;

use App\Interfaces\ArchivoCarga\TabArchivoDetalleRepositoryInterface;
use App\Interfaces\TabArchivoSolicitudesDetalleRepositoryInterface;
use App\Models\ArchivoCarga\TabArchivoSolicitudDetalle;


class TabArchivoSolicitudesDetalleRepository implements TabArchivoSolicitudesDetalleRepositoryInterface
{
    public function getAll()
    {
        return TabArchivoSolicitudDetalle::all();
    }

    public function getByIDSolicitudDeta($id)
    {
        return TabArchivoSolicitudDetalle::where('id_solicitud_detalle', $id)->get();
    }
    public function getByID($id)
    {
        $registro = TabArchivoSolicitudDetalle::where('id', $id)->first();

        if ($registro) {
            $registro->makeVisible('archivo');
        }
        return $registro;
    }

    public function store($data)
    {
        return TabArchivoSolicitudDetalle::create($data);
    }

    public function delete($id)
    {
        return TabArchivoSolicitudDetalle::where('id', $id)->delete();
    }

    public function update(array $data, $id)
    {
        return TabArchivoSolicitudDetalle::where('id', $id)->update($data);
    }
}
