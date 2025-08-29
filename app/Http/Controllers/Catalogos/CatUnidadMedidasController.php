<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\CatUnidadMedida;
use App\Repositories\Catalogos\CatUnidadMedidasRepository;
use Illuminate\Http\Request;
use App\Classes\ApiResponseHelper;
use App\Http\Requests\Catalogos\Store\StoreCatUnidadMedidasRequest;
use App\Http\Requests\Catalogos\Update\UpdateCatUnidadMedidaRequest;
use App\Interfaces\Catalogos\CatUnidadMedidasRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class CatUnidadMedidasController extends Controller
{
    protected $_catUnidadMedidas;

    public function __construct(CatUnidadMedidasRepositoryInterface $catUnidadMedidas)
{
    $this->_catUnidadMedidas = $catUnidadMedidas;
}

    public function getAll()
    {
        try {
            $getAll = $this->_catUnidadMedidas->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido',200);
        }
        catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista',500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_catUnidadMedidas->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatUnidadMedidasRequest $cat){
        DB::beginTransaction();
        try {
            $data = [
                'clave_unidad_medida'=> $cat->clave_unidad_medida,
                'descripcion_unidad_medida'=>$cat->descripcion_unidad_medida,
                'habilitado'=> $cat->habilitado
            ];
            $almacen = $this->_catUnidadMedidas->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Unidad de Medida creada correctamente',201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateCatUnidadMedidaRequest $cat,$id){
        DB::beginTransaction();
        try {
            $data = [
                'clave_unidad_medida'=> $cat->clave_unidad_medida,
                'descripcion_unidad_medida'=>$cat->descripcion_unidad_medida,
                'habilitado'=> $cat->habilitado
            ];
            $this->_catUnidadMedidas->update($data,$id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Unidad de Medida actualizada correctamente',200);
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
            $archivo = $this->_catUnidadMedidas->exportar($filtro);
            DB::commit();
            return ApiResponseHelper::sendResponse($archivo, 'Unidades de medida exportadas correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
