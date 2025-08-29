<?php

namespace App\Repositories;

use App\Interfaces\TabSolicitudesDetalleRepositoryInterface;


use App\Models\SistemaTickets\TabSolicitudDetalle;

class TabSolicitudesDetalleRepository implements TabSolicitudesDetalleRepositoryInterface
{
    public function getAll()
    {
        return TabSolicitudDetalle::orderBy('created_at', 'asc')->get();
    }

    public function getByIDSolicitud($id)
    {
        return TabSolicitudDetalle::orderBy('created_at', 'asc')->where('habilitado', true)->where('id_solicitud', $id)->get();
    }
    public function getByID($id)
    {
        return TabSolicitudDetalle::where('id', $id)->first();
    }


    public function store(array $data)
    {
        return TabSolicitudDetalle::create($data);
    }

    public function update(array $data, $id)
    {
        return TabSolicitudDetalle::where('id', $id)->update($data);
    }
    public function deleteByDetalle($id)
    {

        $data = TabSolicitudDetalle::where('id', $id)->first();

        if ($data) {
            $data->habilitado = 0;
            $data->save(); // Guarda los cambios en la base de datos
        }
        return $data;
    }

    public function deleteBySolicitud($id)
    {
         $data=TabSolicitudDetalle::where('id_solicitud', $id)
            ->update(['habilitado' => 0]);
            return $data;
    }
}
