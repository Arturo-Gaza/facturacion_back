<?php

namespace App\Repositories\ArchivoCarga;

use App\Interfaces\ArchivoCarga\TabObservacionesRepositoryInterface;
use App\Models\ArchivoCarga\TabObservaciones;
use Illuminate\Support\Facades\DB;

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

        $data = TabObservaciones::select(
            'tab_observaciones.id',
            'tab_detalle_cargas.cve_carga',
            'users.name',
            'users.apellidoP',
            'users.apellidoM',
            'users.user',
            'cat_roles.nombre',
            'tab_observaciones.observacion',
            'tab_observaciones.habilitado',
            'tab_observaciones.created_at',

        )
            ->join('users', 'users.id', '=', 'tab_observaciones.id_usuario')
            ->join('tab_detalle_cargas', 'tab_detalle_cargas.id', '=', 'tab_observaciones.id_detalle_carga')
            ->join('cat_roles', 'cat_roles.id', '=', 'users.idRol')
            ->orWhere('tab_observaciones.id_detalle_carga', '=', $idCarga)
            ->get();

        return $data;




        // return DB::table('tab_observaciones')
        //     ->join('users', 'users.id', '=', 'tab_observaciones.id_detalle_carga')
        //     // ->join('tab_detalle_cargas', 'tab_detalle_cargas.id', '=', 'tab_observaciones.id_usuario')
        //     ->where('tab_observaciones.id_detalle_carga', $idCarga)
        //     ->select(
        //         'tab_observaciones.id',
        //         // 'tab_detalle_cargas.cve_carga',
        //         'users.name',
        //         'users.apellidoP',
        //         'users.apellidoM',
        //         'users.user',
        //         'users.idRol',
        //         'tab_observaciones.observacion',
        //         'tab_observaciones.habilitado',
        //     )
        //     ->get();
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
