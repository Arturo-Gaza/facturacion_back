<?php

namespace App\Http\Controllers\SistemaTickets;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SistemaTickets\Store\StoreCatTiposRequest;
use App\Http\Requests\SistemaTickets\Update\UpdateCatTiposRequest;
use App\Interfaces\SistemaTickets\CatTiposRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatTiposController extends Controller
{
    protected $_catTipos;

    public function __construct(CatTiposRepositoryInterface $catTipos)
    {
        $this->_catTipos = $catTipos;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_catTipos->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_catTipos->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function getByDpto($id)
    {
        try {
            $getById = $this->_catTipos->getByDpto($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogos obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatTiposRequest $cat)
    {
        DB::beginTransaction();
        try {
            $data = [
                'descripcion' => $cat->descripcion,
                'req_marca_modelo' => $cat->req_marca_modelo,
                'habilitado' => $cat->habilitado
            ];
            $almacen = $this->_catTipos->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Tipo creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateCatTiposRequest $cat, $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'descripcion' => $cat->descripcion,
                'req_marca_modelo' => $cat->req_marca_modelo,
                'habilitado' => $cat->habilitado
            ];
            $this->_catTipos->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Tipo actualizado correctamente', 200);
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
            $archivo = $this->_catTipos->exportar($filtro);
            DB::commit();
            return ApiResponseHelper::sendResponse($archivo, 'Tipos exportados correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
