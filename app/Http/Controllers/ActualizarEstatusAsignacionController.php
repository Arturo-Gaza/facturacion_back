<?php

namespace App\Http\Controllers;

use App\Models\AsignacionCarga\tab_asignacion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActualizarEstatusAsignacionController extends Controller
{
    public function actualizarEstatus(Request $request, $idUser, $idcarga)
    {

        $request->validate([
            'id_estatus' => 'required|exists:cat_estatuses,id',
        ]);

        $asignacion = tab_asignacion::where('id_usuario', $idUser)
            ->where('id_carga', $idcarga)
            ->first();


        if (!$asignacion) {
            return response()->json([
                'message' => 'Asignación no encontrada'
            ], 404);
        }

        $asignacion->id_estatus = $request->id_estatus;
        $asignacion->save();

        return response()->json([
            'message' => 'Estatus actualizado correctamente',

        ]);
    }

    public function actualizarEstatusFechaInicio(Request $request, $idUser, $idcarga)
    {

        $request->validate([
            'id_estatus' => 'required|exists:cat_estatuses,id',
        ]);

        $asignacion = tab_asignacion::where('id_usuario', $idUser)
            ->where('id_carga', $idcarga)
            ->first();


        if (!$asignacion) {
            return response()->json([
                'message' => 'Asignación no encontrada'
            ], 404);
        }

        $asignacion->id_estatus = $request->id_estatus;
        $asignacion->fecha_inicio_conteo = Carbon::now();
        $asignacion->save();

        return response()->json([
            'message' => 'Estatus actualizado correctamente',

        ]);
    }

    public function actualizarEstatusFechaFin(Request $request, $idUser, $idcarga)
    {

        $request->validate([
            'id_estatus' => 'required|exists:cat_estatuses,id',
        ]);

        $asignacion = tab_asignacion::where('id_usuario', $idUser)
            ->where('id_carga', $idcarga)
            ->first();


        if (!$asignacion) {
            return response()->json([
                'message' => 'Asignación no encontrada'
            ], 404);
        }

        $asignacion->id_estatus = $request->id_estatus;
        $asignacion->fecha_fin_conteo = Carbon::now();
        $asignacion->save();

        return response()->json([
            'message' => 'Estatus actualizado correctamente',

        ]);
    }
}
