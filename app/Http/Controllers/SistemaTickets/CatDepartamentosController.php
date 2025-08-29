<?php

namespace App\Http\Controllers\SistemaTickets;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SistemaTickets\Store\StoreCatDepartamentosRequest;
use App\Http\Requests\SistemaTickets\Update\UpdateCatDepartamentosRequest;
use App\Interfaces\SistemaTickets\CatDepartamentosRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatDepartamentosController extends Controller
{
    protected $_catDepartamentos;

    public function __construct(CatDepartamentosRepositoryInterface $catDepartamentos)
    {
        $this->_catDepartamentos = $catDepartamentos;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_catDepartamentos->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_catDepartamentos->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatDepartamentosRequest $cat)
    {
        DB::beginTransaction();
        try {
            $data = [
                'descripcion' => $cat->descripcion,
                'habilitado' => $cat->habilitado,
                'id_usuario_responsable_compras' => $cat->id_usuario_responsable_compras
            ];
            $almacen = $this->_catDepartamentos->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Departamento creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateCatDepartamentosRequest $cat, $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'descripcion' => $cat->descripcion,
                'habilitado' => $cat->habilitado,
                'id_usuario_responsable_compras' => $cat->id_usuario_responsable_compras
            ];
            $this->_catDepartamentos->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Departamento actualizado correctamente', 200);
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
            $archivo = $this->_catDepartamentos->exportar($filtro);
            DB::commit();
            return ApiResponseHelper::sendResponse($archivo, 'Departamentos exportados correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
