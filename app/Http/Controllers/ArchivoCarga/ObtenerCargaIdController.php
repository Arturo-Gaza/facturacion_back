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
    { {
            try {
                $registros = tab_detalle_archivo::select(
                    'tab_detalle_archivos.id',
                    // 'tab_detalle_archivos.id_almacen',
                    // 'tab_detalle_archivos.id_cat_prod',
                    // 'tab_detalle_archivos.id_unid_med',
                    // 'tab_detalle_archivos.id_gpo_familia',
                    'cat_almacenes.clave_almacen',
                    'cat_productos.clave_producto',
                    'cat_productos.descripcion_producto_material',
                    'cat_unidad_medidas.clave_unidad_medida',
                    'cat_gpo_familias.clave_gpo_familia',
                    'tab_detalle_archivos.libre_utilizacion',
                    'tab_detalle_archivos.en_control_calidad',
                    'tab_detalle_archivos.bloqueado',
                    'tab_detalle_archivos.valor_libre_util',
                    'tab_detalle_archivos.valor_insp_cal',
                    'tab_detalle_archivos.valor_stock_bloq',
                    'tab_detalle_archivos.cantidad_total',
                    'tab_detalle_archivos.importe_unitario',
                    'tab_detalle_archivos.importe_total',
                    'tab_detalle_archivos.habilitado',
                )
                    ->join('cat_almacenes', 'cat_almacenes.id', '=', 'tab_detalle_archivos.id_almacen')
                    ->join('cat_productos', 'cat_productos.id', '=', 'tab_detalle_archivos.id_cat_prod')
                    ->join('cat_unidad_medidas', 'cat_unidad_medidas.id', '=', 'tab_detalle_archivos.id_unid_med')
                    ->join('cat_gpo_familias', 'cat_gpo_familias.id', '=', 'tab_detalle_archivos.id_gpo_familia')
                    ->where('id_carga_cab', $id_carga_cab)->get();

                // $registros = tab_detalle_archivo::where('id_carga_cab', $id_carga_cab)->get();

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
