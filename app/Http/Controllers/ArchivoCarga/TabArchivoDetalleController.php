<?php

namespace App\Http\Controllers\ArchivoCarga;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArchivoCarga\Store\StoreTabArchivoDetalleRequest;
use App\Http\Requests\ArchivoCarga\Update\UpdateTabArchivoDetalleRequest;
use App\Interfaces\ArchivoCarga\TabArchivoDetalleRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabArchivoDetalleController extends Controller
{
    protected $_tabDetalleArchivo;

    public function __construct(TabArchivoDetalleRepositoryInterface $tabDetalleArchivo)
    {
        $this->_tabDetalleArchivo = $tabDetalleArchivo;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_tabDetalleArchivo->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_tabDetalleArchivo->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreTabArchivoDetalleRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_carga_cab' => $request->id_carga_cab,
                'id_almacen' => $request->id_almacen,
                'id_cat_prod' => $request->id_cat_prod,
                'id_unid_med' => $request->id_unid_med,
                'id_gpo_familia' => $request->id_gpo_familia,
                'Libre_utilizacion' => $request->Libre_utilizacion,
                'En_control_calidad' => $request->En_control_calidad,
                'Bloqueado' => $request->Bloqueado,
                'Valor_libre_util' => $request->Valor_libre_util,
                'Valor_en_insp_cal' => $request->Valor_en_insp_cal,
                'Valor_stock_bloq' => $request->Valor_stock_bloq,
                'Cantidad_total' => $request->Cantidad_total,
                'Importe_unitario' => $request->Importe_unitario,
                'Importe_total' => $request->Importe_total,
                'habilitado' => $request->habilitado,
            ];

            $detalleCarga = $this->_tabDetalleArchivo->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Registro creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateTabArchivoDetalleRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_carga_cab' => $request->id_carga_cab,
                'id_almacen' => $request->id_almacen,
                'id_cat_prod' => $request->id_cat_prod,
                'id_unid_med' => $request->id_unid_med,
                'id_gpo_familia' => $request->id_gpo_familia,
                'Libre_utilizacion' => $request->Libre_utilizacion,
                'En_control_calidad' => $request->En_control_calidad,
                'Bloqueado' => $request->Bloqueado,
                'Valor_libre_util' => $request->Valor_libre_util,
                'Valor_en_insp_cal' => $request->Valor_en_insp_cal,
                'Valor_stock_bloq' => $request->Valor_stock_bloq,
                'Cantidad_total' => $request->Cantidad_total,
                'Importe_unitario' => $request->Importe_unitario,
                'Importe_total' => $request->Importe_total,
                'habilitado' => $request->habilitado,
            ];
            $this->_tabDetalleArchivo->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
