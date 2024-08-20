<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArchivoCompletoController extends Controller
{
    public function getCargasByUsuario($id_usuario)
    {
        $result = DB::table('tab_detalle_cargas')
            ->where('id_usuario', $id_usuario)
            ->select('id as id_carga')
            ->get();

        return response()->json($result);
    }
}

