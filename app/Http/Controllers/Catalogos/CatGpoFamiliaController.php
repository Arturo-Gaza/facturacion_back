<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Interfaces\Catalogos\CatGpoFamiliaRepositoryInterface;
use Illuminate\Http\Request;
use App\Classes\ApiResponseHelper;
use App\Http\Requests\Catalogos\Store\StoreCatGpoFamiliaRequest;
use App\Http\Requests\Catalogos\Update\UpdateGpoFamiliaRequest;
use Exception;
use Illuminate\Support\Facades\DB;

class CatGpoFamiliaController extends Controller
{

    protected $_catGpoFamilia;

    public function __construct(CatGpoFamiliaRepositoryInterface $catGpoFamilia)
    {
        $this->_catGpoFamilia = $catGpoFamilia;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_catGpoFamilia->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido',200);
        }
        catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista',500);
        }
    }

    public function getAllPersonalizado($idCarga)
    {
        try {
            $getAllPersonalizado = $this->_catGpoFamilia->getAllPersonalizado($idCarga);
            return ApiResponseHelper::sendResponse($getAllPersonalizado, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_catGpoFamilia->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatGpoFamiliaRequest $cat){
        DB::beginTransaction();
        try {
            $data = [
                'clave_gpo_familia'=> $cat->clave_gpo_familia,
                'descripcion_gpo_familia'=>$cat->descripcion_gpo_familia,
                'habilitado'=> $cat->habilitado
            ];
            $grupo = $this->_catGpoFamilia->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo creado correctamente',201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateGpoFamiliaRequest $cat,$id){
        DB::beginTransaction();
        try {
            $data = [
                'clave_gpo_familia'=> $cat->clave_gpo_familia,
                'descripcion_gpo_familia'=>$cat->descripcion_gpo_familia,
                'habilitado'=> $cat->habilitado
            ];
            $this->_catGpoFamilia->update($data,$id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo actualizado correctamente',200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
