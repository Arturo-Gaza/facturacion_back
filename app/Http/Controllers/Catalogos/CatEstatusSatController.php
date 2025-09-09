<?php

namespace App\Http\Controllers\Catalogos;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\Store\StoreCatEstatusSat;
use App\Http\Requests\Catalogos\Update\UpdateCatEstatusSat;
use App\Interfaces\Catalogos\CatEstatusSatRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatEstatusSatController extends Controller
{
    protected $estatuses;

    public function __construct(CatEstatusSatRepositoryInterface $estatuses)
    {
        $this->estatuses = $estatuses;
    }

    public function getAll()
    {
        try {
            $all = $this->estatuses->getAll();
            return ApiResponseHelper::sendResponse($all, 'Estatuses SAT obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $estatus = $this->estatuses->getByID($id);
            if (!$estatus) {
                return ApiResponseHelper::rollback(null, 'Estatus no encontrado', 404);
            }
            return ApiResponseHelper::sendResponse($estatus, 'Estatus obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatEstatusSat $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only(['clave', 'descripcion', 'vigente']);
            $estatus = $this->estatuses->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse($estatus, 'Estatus SAT creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateCatEstatusSat $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->only(['clave', 'descripcion', 'vigente']);
            $updated = $this->estatuses->update($data, $id);

            if (!$updated) {
                DB::rollBack();
                return ApiResponseHelper::rollback(null, 'Estatus no encontrado', 404);
            }

            DB::commit();
            return ApiResponseHelper::sendResponse($updated, 'Estatus SAT actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
