<?php

namespace App\Repositories\AsignacionCarga;

use App\Interfaces\AsignacionCarga\TabAsignacionInterface;
use App\Models\ArchivoConteo\TabConteo;
use App\Models\AsignacionCarga\tab_asignacion;

class TabAsigancionRepository implements TabAsignacionInterface
{
    public function getAll()
    {
        return tab_asignacion::all();
    }

    public function getByID($id): ?tab_asignacion
    {
        return tab_asignacion::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return tab_asignacion::create($data);
    }

    public function update(array $data, $id)
    {
        return tab_asignacion::where('id', $id)->update($data);
    }
    public function getByIdCargaIdUser($idCarga,$idUser): ?tab_asignacion
    {
        return tab_asignacion::where('id_carga', $idCarga)->where('id_usuario', $idUser)->first();
    }

}
