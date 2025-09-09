<?php

namespace App\Http\Controllers\Catalogos;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\Store\StoreCatTipoDireccionRequest;
use App\Http\Requests\Catalogos\Update\updateCatTipoDireccionRequest;
use App\Interfaces\Catalogos\CatTipoDireccionRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatTipoDireccionController extends Controller
{
    protected $tipos;

    public function __construct(CatTipoDireccionRepositoryInterface $tipos)
    {
        $this->tipos = $tipos;
    }

    public function getAll()
    {
        try {
            $all = $this->tipos->getAll();
            return ApiResponseHelper::sendResponse($all, 'Tipos de dirección obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $tipo = $this->tipos->getByID($id);
            if (!$tipo) {
                return ApiResponseHelper::rollback(null, 'Tipo de dirección no encontrado', 404);
            }
            return ApiResponseHelper::sendResponse($tipo, 'Tipo de dirección obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatTipoDireccionRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'clave',
                'descripcion',
                'habilitado']);
            $tipo = $this->tipos->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse($tipo, 'Tipo de dirección creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateCatTipoDireccionRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'clave',
                'descripcion',
                'habilitado']);
            $updated = $this->tipos->update($data, $id);

            if (!$updated) {
                DB::rollBack();
                return ApiResponseHelper::rollback(null, 'Tipo de dirección no encontrado', 404);
            }

            DB::commit();
            return ApiResponseHelper::sendResponse($updated, 'Tipo de dirección actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
