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
    public function getByIdCargaIdUser($idCarga, $idUser): ?tab_asignacion
    {
        return tab_asignacion::where('id_carga', $idCarga)->where('id_usuario', $idUser)->first();
    }

    public function getByIdCargaIdUserper($idCarga, $idUser)
    {
        // return tab_asignacion::where('id_carga', $idCarga)->where('id_usuario', $idUser)->first();

        $usuario = tab_asignacion::select(
            'tab_asignacions.id',
            'tab_asignacions.id_carga',
            'tab_asignacions.id_usuario',
            'tab_asignacions.conteo',
            'tab_asignacions.fecha_asignacion',
            'tab_asignacions.fecha_inicio_conteo',
            'tab_asignacions.fecha_fin_conteo',
            'tab_asignacions.id_estatus',
            'tab_asignacions.habilitado',
            'tab_asignacions.created_at',
            'tab_asignacions.updated_at',
            'cat_estatuses.nombre as estatus',
            'tab_detalle_cargas.cve_carga',
            "users.name",
            "users.apellidoP",
            "users.apellidoM",
            "users.user",
        )
            ->join('users', 'users.id', '=', 'tab_asignacions.id_usuario')
            ->join('tab_detalle_cargas', 'tab_detalle_cargas.id', '=', 'tab_asignacions.id_carga')
            ->join('cat_estatuses', 'cat_estatuses.id', '=', 'tab_asignacions.id_estatus')
            ->where('tab_asignacions.id_carga', $idCarga)
            ->where('tab_asignacions.id_usuario', $idUser)
            ->get();
        $results = array();
        foreach ($usuario as $val) {
            $results[] = $val;
        }

        return $results;
    }
}
