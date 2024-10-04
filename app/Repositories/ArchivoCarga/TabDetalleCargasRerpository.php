<?php

namespace App\Repositories\ArchivoCarga;

use App\Interfaces\ArchivoCarga\TabArchivoCargaRepositoryInterface;
use App\Models\ArchivoCarga\Tab_archivo_completo;
use App\Models\ArchivoCarga\tab_detalle_archivo;
use App\Models\ArchivoCarga\tab_detalle_carga;
use App\Models\ArchivoCarga\TabObservaciones;
use App\Models\ArchivoConteo\TabConteo;
use App\Models\AsignacionCarga\tab_asignacion;

class TabDetalleCargasRerpository implements TabArchivoCargaRepositoryInterface
{
    public function getAll()
    {
        $usuario = tab_detalle_carga::select(
            'tab_detalle_cargas.id',
            'tab_detalle_cargas.cve_carga',
            'tab_detalle_cargas.conteo',
            'tab_detalle_cargas.nombre_archivo',
            'tab_detalle_cargas.id_usuario',
            'tab_detalle_cargas.Reg_Archivo',
            'tab_detalle_cargas.Reg_a_Contar',
            'tab_detalle_cargas.reg_vobo',
            'tab_detalle_cargas.reg_excluidos',
            'tab_detalle_cargas.reg_incorpora',
            'tab_detalle_cargas.id_estatus',
            'tab_detalle_cargas.observaciones',
            'tab_detalle_cargas.habilitado',
            'tab_detalle_cargas.created_at',
            'tab_detalle_cargas.updated_at',
            'users.name',
            'users.apellidoP',
            'users.apellidoM',
            'users.email',
            'users.user',
            'cat_estatuses.nombre AS nombre_estatus'
        )
            ->join('users', 'users.id', '=', 'tab_detalle_cargas.id_usuario')
            ->join('cat_estatuses', 'cat_estatuses.id', '=', 'tab_detalle_cargas.id_estatus')->get();
        return $usuario;
    }

    public function getByID($id): ?tab_detalle_carga
    {
        return tab_detalle_carga::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return tab_detalle_carga::create($data);
    }

    public function update(array $data, $id)
    {
        return tab_detalle_carga::where('id', $id)->update($data);
    }

    public function deleteCarga($idCarga)
    {
        tab_detalle_archivo::where('id_carga_cab', $idCarga)->delete();
        TabConteo::where('id_carga',$idCarga)->delete();
        Tab_archivo_completo::where('id_detalle_carga',$idCarga)->delete();
        tab_asignacion::where('id_carga',$idCarga)->delete();
        TabObservaciones::where('id_detalle_carga',$idCarga)->delete();
        tab_detalle_carga::where('id',$idCarga)->delete();
        return "Carga eliminada correctamente.";
    }

    public function validarEstatusCarga($idCarga,$conteo)
    {
        $estatus = [6,8,9];
        return tab_detalle_carga::where('id', $idCarga)->where('conteo',$conteo)->whereIn('id_estatus',$estatus)->count();
    }
}
