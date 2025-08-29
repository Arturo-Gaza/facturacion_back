<?php

namespace App\Http\Controllers\SistemaTickets;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SistemaTickets\Store\StoreCatCentroRequest;
use App\Http\Requests\SistemaTickets\Update\UpdateCatCentroRequest;
use App\Interfaces\SistemaTickets\CatCentroRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatCentroController extends Controller
{
    protected $_catCentro;

    public function __construct(CatCentroRepositoryInterface $catCentro)
    {
        $this->_catCentro = $catCentro;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_catCentro->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido',200);
        }
        catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista',500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_catCentro->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatCentroRequest $cat){
        DB::beginTransaction();
        try {
            $data = [
                'clave_centro'=> $cat->clave_centro,
                'descripcion_centro'=>$cat->descripcion_centro,
                'habilitado'=> $cat->habilitado
            ];
            $almacen = $this->_catCentro->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo creado correctamente',201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateCatCentroRequest $cat,$id){
        DB::beginTransaction();
        try {
            $data = [
                'clave_centro'=> $cat->clave_centro,
                'descripcion_centro'=>$cat->descripcion_centro,
                'habilitado'=> $cat->habilitado
            ];
            $this->_catCentro->update($data,$id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo actualizado correctamente',200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
