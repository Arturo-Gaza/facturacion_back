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
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista',500);
        }
    }

    public function getAllPersonalizado($idCarga)
    {
        try {
            $getAllPersonalizado = $this->_catGpoFamilia->getAllPersonalizado($idCarga);
            return ApiResponseHelper::sendResponse($getAllPersonalizado, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_catGpoFamilia->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatGpoFamiliaRequest $cat){
        DB::beginTransaction();
        try {
            $data = [
                'clave_gpo_familia'=> $cat->clave_gpo_familia,
                'descripcion_gpo_familia'=>$cat->descripcion_gpo_familia,
                'descripcion_gpo_familia_2' =>  $cat->descripcion_gpo_familia_2,
                'habilitado'=> $cat->habilitado
            ];
            $grupo = $this->_catGpoFamilia->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Grupo creado correctamente',201);
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
                'descripcion_gpo_familia_2' =>  $cat->descripcion_gpo_familia_2,
                'habilitado'=> $cat->habilitado
            ];
            $this->_catGpoFamilia->update($data,$id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Grupo actualizado correctamente',200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function search(Request $req)
    {
        try {
            $data = [
                'categoria' => $req->categoria,
                'termino' => $req->termino,
            ];
            $getAll = $this->_catGpoFamilia->search($data);
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function exportar(Request $data)
    {
        DB::beginTransaction();
        try {
            $filtro = trim($data->getContent(), '"');
            $archivo = $this->_catGpoFamilia->exportar($filtro);
            DB::commit();
            return ApiResponseHelper::sendResponse($archivo, 'Grupo Articulos exportados correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

}
