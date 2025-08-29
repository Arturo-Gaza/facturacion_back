<?php

namespace App\Http\Controllers\SistemaTickets;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SistemaTickets\Store\StoreCatMonedaRequest;
use App\Http\Requests\SistemaTickets\Update\UpdateCatMonedaRequest;
use App\Interfaces\SistemaTickets\CatMonedaRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatMonedaController extends Controller
{
    protected $_catMoneda;

    public function __construct(CatMonedaRepositoryInterface $catMoneda)
    {
        $this->_catMoneda = $catMoneda;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_catMoneda->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_catMoneda->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatMonedaRequest $cat)
    {
        DB::beginTransaction();
        try {
            $data = [
                'clave_moneda' => $cat->clave_moneda,
                'descripcion_moneda' => $cat->descripcion_moneda,
                'habilitado' => $cat->habilitado
            ];
            $almacen = $this->_catMoneda->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Moneda creada correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateCatMonedaRequest $cat, $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'clave_moneda' => $cat->clave_moneda,
                'descripcion_moneda' => $cat->descripcion_moneda,
                'habilitado' => $cat->habilitado
            ];
            $this->_catMoneda->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Moneda actualizada correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function exportar(Request $data)
    {

        DB::beginTransaction();
        try {
            $filtro = trim($data->getContent(), '"');

            $archivo = $this->_catMoneda->exportar($filtro);
            DB::commit();
            return ApiResponseHelper::sendResponse($archivo, 'Monedas exportadas correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
