<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Interfaces\Catalogos\CatUbicacionesRepositoryInterface;
use App\Classes\ApiResponseHelper;
use App\Http\Requests\Catalogos\Store\StoreCatUbicacionesRequest;
use App\Http\Requests\Catalogos\Update\UpdateCatUbicacionesRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatUbicaionesController extends Controller
{
    protected $_catUbicaciones;

    public function __construct(CatUbicacionesRepositoryInterface $catUbicaciones)
    {
        $this->_catUbicaciones = $catUbicaciones;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_catUbicaciones->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido',200);
        }
        catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista',500);
        }
    }

    public function getAllPaginate(Request $data)
    {
        try {
            $getAll = $this->_catUbicaciones->getAllPaginate($data);
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido',200);
        }
        catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista',500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_catUbicaciones->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatUbicacionesRequest $cat){
        DB::beginTransaction();
        try {
            $data = [
                'clave_ubicacion'=> $cat->clave_ubicacion,
                'descripcion_ubicacion'=>$cat->descripcion_ubicacion,
                'habilitado'=> $cat->habilitado
            ];
            $ubicacion = $this->_catUbicaciones->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo creado correctamente',201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateCatUbicacionesRequest $cat,$id){
        DB::beginTransaction();
        try {
            $data = [
                'clave_ubicacion'=> $cat->clave_ubicacion,
                'descripcion_ubicacion'=>$cat->descripcion_ubicacion,
                'habilitado'=> $cat->habilitado
            ];
            $this->_catUbicaciones->update($data,$id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo actualizado correctamente',200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
