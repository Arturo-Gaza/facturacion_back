<?php

namespace App\Http\Controllers\ArchivoCarga;

use App\Http\Controllers\Controller;
use App\Models\ArchivoCarga\Tab_archivo_completo;
use Illuminate\Http\Request;
use App\Classes\ApiResponseHelper;
use App\Models\ArchivoCarga\tab_detalle_archivo;
use Exception;

class ObtenerCargaIdController extends Controller
{
    /**
     * Retrieve records from tab_archivo_completos by id_detalle_carga
     *
     * @param int $id_detalle_carga
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByDetalleCargaId($id_carga_cab)
    {
        {
            try {
              
                $registros = tab_detalle_archivo::where('id_carga_cab', $id_carga_cab)->get();
    
                if ($registros->isEmpty()) {
                    return ApiResponseHelper::sendResponse([], 'No se encontraron registros para el id_carga_cab proporcionado', 404);
                }
    
                return ApiResponseHelper::sendResponse($registros, 'Registros encontrados', 200);
            } catch (Exception $ex) {
                return ApiResponseHelper::sendResponse($ex->getMessage(), 'El registro no se encuentra', 500);
            }
        }
    }
}
