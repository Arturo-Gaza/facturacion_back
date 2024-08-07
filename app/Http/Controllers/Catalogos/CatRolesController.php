<?php

namespace App\Http\Controllers\Catalogos;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\Store\StoreCatRolesRequest;
use App\Http\Requests\Catalogos\Update\UpdateCatRolesRequest;
use App\Interfaces\Catalogos\CatRolesRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatRolesController extends Controller
{
    protected $_catRoles;

    public function __construct(CatRolesRepositoryInterface $catRoles)
    {
        $this->_catRoles = $catRoles;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_catRoles->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido',200);
        }
        catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista',500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_catRoles->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatRolesRequest $cat){
        DB::beginTransaction();
        try {
            $data = [
                'nombre'=> $cat->nombre,
                'habilitado'=> $cat->habilitado
            ];
            $student = $this->_catRoles->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo creado correctamente',201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateCatRolesRequest $cat,$id){
        DB::beginTransaction();
        try {
            $data = [
                'nombre'=> $cat->nombre,
                'habilitado'=> $cat->habilitado
            ];
            $this->_catRoles->update($data,$id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo actualizado correctamente',200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
