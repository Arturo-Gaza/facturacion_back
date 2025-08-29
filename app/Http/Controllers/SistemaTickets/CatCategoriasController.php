<?php

namespace App\Http\Controllers\SistemaTickets;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SistemaTickets\Store\StoreCatCategoriasRequest;
use App\Http\Requests\SistemaTickets\Update\UpdateCatCategoriasRequest;
use App\Interfaces\SistemaTickets\CatCategoriasRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatCategoriasController extends Controller
{
    protected $_catCategoria;

    public function __construct(CatCategoriasRepositoryInterface $catCategoria)
    {
        $this->_catCategoria = $catCategoria;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_catCategoria->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_catCategoria->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function getByIdCat($id)
    {
        try {
            $getById = $this->_catCategoria->getByIdCat($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function getByDpto($id)
    {
        try {
            $getById = $this->_catCategoria->getByDpto($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatCategoriasRequest $cat)
    {
        DB::beginTransaction();
        try {
            $data = [
                'descripcion_categoria' => $cat->descripcion_categoria,
                'id_tipo' => $cat->id_tipo,
                'habilitado' => $cat->habilitado
            ];
            $almacen = $this->_catCategoria->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Categoria creada correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateCatCategoriasRequest $cat, $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'descripcion_categoria' => $cat->descripcion_categoria,
                'id_tipo' => $cat->id_tipo,
                'habilitado' => $cat->habilitado
            ];
            $this->_catCategoria->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Categoria actualizada correctamente', 200);
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
            $archivo = $this->_catCategoria->exportar($filtro);
            DB::commit();
            return ApiResponseHelper::sendResponse($archivo, 'Categorias exportadas correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
