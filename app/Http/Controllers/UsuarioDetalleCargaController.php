<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use Illuminate\Support\Facades\DB;

class UsuarioDetalleCargaController extends Controller
{
    public function UsuarioDetalleCarga($idUser)
    {
        $resultados = DB::table('tab_asignacions')
        ->join('tab_detalle_cargas', 'tab_detalle_cargas.id', '=', 'tab_asignacions.id_carga')
        ->join('cat_estatuses', 'cat_estatuses.id', '=', 'tab_detalle_cargas.id_estatus')
        ->where('tab_asignacions.id_usuario', $idUser)  
        ->where('tab_asignacions.habilitado', true)
        ->select(
            'tab_detalle_cargas.id',
            'tab_detalle_cargas.cve_carga',
            'tab_detalle_cargas.created_at',
            'tab_detalle_cargas.conteo',
            'tab_detalle_cargas.nombre_archivo',
            'tab_detalle_cargas.id_usuario',
            'tab_detalle_cargas.Reg_Archivo',
            'tab_detalle_cargas.Reg_a_Contar',
            'tab_detalle_cargas.reg_vobo',
            'tab_detalle_cargas.reg_excluidos',
            'tab_detalle_cargas.reg_incorpora',
            'cat_estatuses.nombre as status_nombre',
            'tab_detalle_cargas.habilitado'
        )
        ->get();

        $data1 = array();
        foreach ($resultados as $val) {
            $data1[] = $val;
        }
        return ApiResponseHelper::sendResponse($resultados, 'Carga obtenido',200);
    }
}
