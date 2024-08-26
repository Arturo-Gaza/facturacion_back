<?php

namespace App\Http\Controllers\ArchivoCarga;

use App\Http\Controllers\Controller;
use App\Models\ArchivoCarga\tab_detalle_carga;
use Illuminate\Http\Request;
use App\Classes\ApiResponseHelper;
use Exception;

class ActualizarStatusController extends Controller
{
    public function actualizarEstatus(Request $request, $id)
    {
        try {
        
            $request->validate([
                'id_estatus' => 'required|exists:cat_estatuses,id',
            ]);

            $detalleCarga = tab_detalle_carga::find($id);

            if (!$detalleCarga) {
                return ApiResponseHelper::sendResponse([], 'Registro no encontrado', 404);
            }

            $detalleCarga->id_estatus = $request->id_estatus;
            $detalleCarga->save();
            return ApiResponseHelper::sendResponse($detalleCarga, 'Estatus actualizado correctamente', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex->getMessage(), 'Ocurrió un error al actualizar el estatus', 500);
        }
    }
}
