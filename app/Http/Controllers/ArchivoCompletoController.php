<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArchivoCompletoController extends Controller
{
    public function getCargasByUsuario($id_usuario)
    {
        $result = DB::table('tab_detalle_cargas')
            ->where('id_usuario', $id_usuario)
            ->select(
                'id as id_carga',  // Alias para el ID de la carga
                'cve_carga',       // Nombre correcto de la columna
                'fecha_asignacion',
                'conteo',
                'nombre_archivo',
                'Reg_Archivo',
                'Reg_a_Contar',
                'reg_vobo',
                'reg_excluidos',
                'reg_incorpora',
                'habilitado'
            )
            ->get();

        return response()->json($result);
    }
}

