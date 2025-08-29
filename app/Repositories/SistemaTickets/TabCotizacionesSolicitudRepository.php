<?php

namespace App\Repositories\SistemaTickets;

use App\Interfaces\ArchivoCarga\TabObservacionesRepositoryInterface;
use App\Interfaces\SistemaTickets\TabCotizacionesDetalleRepositoryInterface;
use App\Interfaces\SistemaTickets\TabCotizacionesSolicitudRepositoryInterface;
use App\Interfaces\SistemaTickets\TabObesrvacionesDetalleRepositoryInterface;
use App\Models\SistemaTickets\TabCotizacionesSolicitudes;
use App\Models\SistemaTickets\TabSolicitud;

class TabCotizacionesSolicitudRepository implements TabCotizacionesSolicitudRepositoryInterface
{
    public function getAll()
    {
        return TabCotizacionesSolicitudes::all();
    }

    public function getByID($id): ?TabCotizacionesSolicitudes
    {
        $registro = TabCotizacionesSolicitudes::where('id', $id)->first();

        if ($registro) {
            $registro->makeVisible('archivo_cotizacion');
        }
        return $registro;
    }
    public function getByIdDetalle($id)
    {
        return TabCotizacionesSolicitudes::where('id_solicitud', $id)->get();
    }

    public function store(array $data)
    {
        $id = $data["id_solicitud"];
        $solicitud = TabSolicitud::find($id);
        if (!$solicitud) {
            return null;
        }
        $solicitud->cotizacion_global = true;
        $solicitud->update();
        return TabCotizacionesSolicitudes::create($data);
    }

    public function update(array $data, $id)
    {
        return TabCotizacionesSolicitudes::where('id', $id)->update($data);
    }
    public function delete($id)
    {
        return TabCotizacionesSolicitudes::where('id', $id)->delete();
    }
}
