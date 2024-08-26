<?php

namespace App\Http\Controllers;

use App\Models\ArchivoCarga\tab_detalle_carga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioDetalleCargaController extends Controller
{
    public function UsuarioDetalleCarga()
    {
        // $usuarioAsigndo = tab_detalle_carga::select(
        //     'tab_detalle_cargas.id',
        //     'tab_detalle_cargas.cve_carga'
        // )
        //     ->join('tab_asignacions', 'tab_asignacions.id', '=', 'tab_detalle_cargas.id')
        //     ->where('tab_detalle_cargas.id_usuario', 1)
        //     ->get();


        $resultados = DB::table('tab_detalle_cargas')
        ->join('tab_asignacions', 'tab_asignacions.id', '=', 'tab_detalle_cargas.id')
        ->where('tab_detalle_cargas.id_usuario', 1)
        ->select('tab_detalle_cargas.id',
                 'tab_detalle_cargas.cve_carga',
                 'tab_detalle_cargas.conteo',
                 'tab_detalle_cargas.nombre_archivo',
                 'tab_detalle_cargas.id_usuario',
                 'tab_detalle_cargas.Reg_Archivo',
                 'tab_detalle_cargas.Reg_a_Contar',
                 'tab_detalle_cargas.reg_vobo',
                 'tab_detalle_cargas.reg_excluidos',
                 'tab_detalle_cargas.reg_incorpora',
                 'tab_detalle_cargas.id_estatus',)
        ->get();

        $data1 = array();
        foreach ($resultados as $val) {
            $data1[] =$val;
        }
    return response()->json($data1);
    }
}
