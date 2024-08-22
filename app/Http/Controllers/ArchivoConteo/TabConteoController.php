<?php

namespace App\Http\Controllers\ArchivoConteo;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArchivoConteo\Store\StoreTabConteoRequest;
use App\Interfaces\ArchivoConteo\TabConteoRepositoryInterface;
use Illuminate\Http\Request;
use App\Classes\ApiResponseHelper;
use App\Http\Requests\ArchivoConteo\Update\UpdateTabConteoRequest;
use Exception;
use Illuminate\Support\Facades\DB;

class TabConteoController extends Controller
{
    protected $_TabConteo;

    public function __construct(TabConteoRepositoryInterface $_TabConteo)
    {
        $this->_TabConteo = $_TabConteo;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_TabConteo->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido',200);
        }
        catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista',500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_TabConteo->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreTabConteoRequest $cat){
        DB::beginTransaction();
        try {
            $data = [
                'id_carga'=> $cat->id_carga,
                'id_producto'=> $cat->id_producto,
                'codigo'=> $cat->codigo,
                'descripcion'=>$cat->descripcion,
                'ume'=> $cat->ume,
                'cantidad'=> $cat->cantidad,
                'ubicacion'=> $cat->ubicacion,
                'observaciones'=> $cat->observaciones
            ];
            $_TabConteo = $this->_TabConteo->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo creado correctamente',201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateTabConteoRequest $cat,$id){
        DB::beginTransaction();
        try {
            $data = [
                'id_carga'=> $cat->id_carga,
                'id_producto'=> $cat->id_producto,
                'codigo'=> $cat->codigo,
                'descripcion'=>$cat->descripcion,
                'ume'=> $cat->ume,
                'cantidad'=> $cat->cantidad,
                'ubicacion'=> $cat->ubicacion,
                'observaciones'=> $cat->observaciones
            ];

            $this->_TabConteo->update($data,$id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo actualizado correctamente',200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
