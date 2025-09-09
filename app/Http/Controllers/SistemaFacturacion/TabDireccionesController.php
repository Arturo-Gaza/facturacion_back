<?php

namespace App\Http\Controllers\SistemaFacturacion;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\Store\StoreCatTipoDireccionRequest;
use App\Http\Requests\SistemaFacturacion\Store\StoreTabDireccionesRequest;
use App\Http\Requests\SistemaFacturacion\Update\UpdateTabDireccionesRequest;
use App\Interfaces\SistemaFacturacion\TabDireccionesRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabDireccionesController extends Controller
{
    protected $_direcciones;

    public function __construct(TabDireccionesRepositoryInterface $direcciones)
    {
        $this->_direcciones = $direcciones;
    }

    public function getAll()
    {
        try {
            $all = $this->_direcciones->getAll();
            return ApiResponseHelper::sendResponse($all, 'Direcciones obtenidas', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $direccion = $this->_direcciones->getByID($id);
            if (!$direccion) {
                return ApiResponseHelper::rollback(null, 'Dirección no encontrada', 404);
            }
            return ApiResponseHelper::sendResponse($direccion, 'Dirección obtenida', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreTabDireccionesRequest $request)
    {
        DB::beginTransaction();
        try {
        $data = $request->only([
                'id_cliente', 'id_tipo_direccion', 'calle', 'num_exterior', 'num_interior',
                'colonia', 'localidad', 'municipio', 'estado', 'codigo_postal', 'pais'
            ]);

            $direccion = $this->_direcciones->store($data);

            DB::commit();
            return ApiResponseHelper::sendResponse($direccion, 'Dirección creada correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateTabDireccionesRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'id_cliente', 'id_tipo_direccion', 'calle', 'num_exterior', 'num_interior',
                'colonia', 'localidad', 'municipio', 'estado', 'codigo_postal', 'pais'
            ]);

            $updated = $this->_direcciones->update($data, $id);

            if (!$updated) {
                DB::rollBack();
                return ApiResponseHelper::rollback(null, 'Dirección no encontrada', 404);
            }

            DB::commit();
            return ApiResponseHelper::sendResponse($updated, 'Dirección actualizada correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
