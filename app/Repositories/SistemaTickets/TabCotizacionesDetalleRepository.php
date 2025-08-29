<?php

namespace App\Repositories\SistemaTickets;

use App\Interfaces\ArchivoCarga\TabObservacionesRepositoryInterface;
use App\Interfaces\SistemaTickets\TabCotizacionesDetalleRepositoryInterface;
use App\Interfaces\SistemaTickets\TabObesrvacionesDetalleRepositoryInterface;
use App\Models\SistemaTickets\TabCotizacionesSolicitudesDetalle;

class TabCotizacionesDetalleRepository implements TabCotizacionesDetalleRepositoryInterface
{
    public function getAll()
    {
        return TabCotizacionesSolicitudesDetalle::all();
    }

    public function getByID($id): ?TabCotizacionesSolicitudesDetalle
    {
        $registro= TabCotizacionesSolicitudesDetalle::where('id', $id)->first();

         if ($registro) {
            $registro->makeVisible('archivo_cotizacion');
        }
        return $registro;
    }
        public function getByIdDetalle($id)
    {
        return TabCotizacionesSolicitudesDetalle::where('id_solicitud_detalle', $id)->get();
    }

    public function store(array $data)
    {
        return TabCotizacionesSolicitudesDetalle::create($data);
    }

    public function update(array $data, $id)
    {
        return TabCotizacionesSolicitudesDetalle::where('id', $id)->update($data);
    }
            public function delete( $id)
    {
        return TabCotizacionesSolicitudesDetalle::where('id',$id)->delete();
    }
}
