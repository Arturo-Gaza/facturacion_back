<?php

namespace App\Http\Controllers\Catalogos;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\Store\StoreCatRegimenesFiscalesRequest;
use App\Http\Requests\Catalogos\Update\UpdateCatRegimenesFiscalesRequest;
use App\Http\Requests\Catalogos\Update\UpdateCatTipoContactoRequest;
use App\Interfaces\Catalogos\CatRegimenesFiscaslesRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatRegimenesFiscalesController extends Controller
{
    protected $_regimenes;

    public function __construct(CatRegimenesFiscaslesRepositoryInterface $regimenes)
    {
        $this->_regimenes = $regimenes;
    }

    public function getAll()
    {
        try {
            $all = $this->_regimenes->getAll();
            return ApiResponseHelper::sendResponse($all, 'Regímenes fiscales obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $regimen = $this->_regimenes->getByID($id);
            if (!$regimen) {
                return ApiResponseHelper::rollback(null, 'Regimen no encontrado', 404);
            }
            return ApiResponseHelper::sendResponse($regimen, 'Regimen obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function getByMoralOFisica($esPersonaMoral)
    {
        try {
            $esPersonaMoral = filter_var($esPersonaMoral, FILTER_VALIDATE_BOOLEAN);

            $regimen = $this->_regimenes->getByMoralOFisica($esPersonaMoral);
            if (!$regimen) {
                return ApiResponseHelper::rollback(null, 'Regimen no encontrado', 404);
            }
            return ApiResponseHelper::sendResponse($regimen, 'Regimen obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatRegimenesFiscalesRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'clave',
                'descripcion',
                'aplica_pf',
                'aplica_pm',
                'habilitado'
            ]);
            $regimen = $this->_regimenes->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse($regimen, 'Regimen fiscal creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateCatRegimenesFiscalesRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'clave',
                'descripcion',
                'aplica_pf',
                'aplica_pm',
                'habilitado'
            ]);
            $updated = $this->_regimenes->update($data, $id);

            if (!$updated) {
                DB::rollBack();
                return ApiResponseHelper::rollback(null, 'Regimen no encontrado', 404);
            }

            DB::commit();
            return ApiResponseHelper::sendResponse($updated, 'Regimen fiscal actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
