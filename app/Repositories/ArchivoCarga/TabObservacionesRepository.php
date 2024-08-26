<?php

namespace App\Repositories\ArchivoCarga;

use App\Interfaces\ArchivoCarga\TabObservacionesRepositoryInterface;
use App\Models\ArchivoCarga\TabObservaciones;

class TabObservacionesRepository implements TabObservacionesRepositoryInterface
{
    public function getAll()
    {
        return TabObservaciones::all();
    }

    public function getByID($id): ?TabObservaciones
    {
        return TabObservaciones::where('id', $id)->first();
    }

    public function getByIDCarga($idCarga)
    {
        return TabObservaciones::where('id_detalle_carga', $idCarga)->get();
    }


    public function store(array $data)
    {
        return TabObservaciones::create($data);
    }

    public function update(array $data, $id)
    {
        return TabObservaciones::where('id', $id)->update($data);
    }
}
