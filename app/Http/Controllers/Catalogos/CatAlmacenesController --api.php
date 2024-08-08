<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Interfaces\Catalogos\CatAlmacenesRepositoryInterface;
use App\Http\Requests\Catalogos\Store\StoreCatAlmacenesRequest;
use App\Http\Requests\Catalogos\Update\UpdateCatAlmacenesRequest;
use Illuminate\Http\Request;
use App\Classes\ApiResponseHelper;
use Exception;
use Illuminate\Support\Facades\DB;

class CatAlmacenesController  extends Controller
{
    protected $_catAlmacenes;

    public function __construct(CatAlmacenesRepositoryInterface $catCatAlmacenes)
    {
        $this->_catAlmacenes = $catCatAlmacenes;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_catAlmacenes->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido',200);
        }
        catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista',500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_catAlmacenes->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatAlmacenesRequest $cat){
        DB::beginTransaction();
        try {
            $data = [
                'clave_almacen'=> $cat->clave_almacen,
                'descripcion_almacen'=>$cat->descripcion_almacen,
                'habilitado'=> $cat->habilitado
            ];
            $student = $this->_catAlmacenes->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo creado correctamente',201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateCatAlmacenesRequest $cat,$id){
        DB::beginTransaction();
        try {
            $data = [
                'clave_almacen'=> $cat->clave_almacen,
                'descripcion_almacen'=>$cat->descripcion_almacen,
                'habilitado'=> $cat->habilitado
            ];
            $this->_catAlmacenes->update($data,$id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo actualizado correctamente',200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

}
