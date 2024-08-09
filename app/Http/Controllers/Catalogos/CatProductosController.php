<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\ApiResponseHelper;
use App\Http\Requests\Catalogos\Store\StoreCatProductosRequest;
use App\Http\Requests\Catalogos\Update\UpdateProductosRequest;
use App\Interfaces\Catalogos\CatProductosRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class CatProductosController extends Controller
{
    protected $_catProductos;

    public function __construct(CatProductosRepositoryInterface $catProductos)
    {
        $this->_catProductos = $catProductos;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_catProductos->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido',200);
        }
        catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista',500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_catProductos->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatProductosRequest $cat){
        DB::beginTransaction();
        try {
            $data = [
                'clave_almacen'=> $cat->clave_almacen,
                'descripcion_almacen'=>$cat->descripcion_almacen,
                'habilitado'=> $cat->habilitado
            ];
            $producto = $this->_catProductos->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo creado correctamente',201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateProductosRequest $cat,$id){
        DB::beginTransaction();
        try {
            $data = [
                'clave_almacen'=> $cat->clave_almacen,
                'descripcion_almacen'=>$cat->descripcion_almacen,
                'habilitado'=> $cat->habilitado
            ];
            $this->_catProductos->update($data,$id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo actualizado correctamente',200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
