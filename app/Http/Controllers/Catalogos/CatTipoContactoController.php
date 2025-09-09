<?php

namespace App\Http\Controllers\Catalogos;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\Store\StoreCatTipoContactoRequest;
use App\Http\Requests\Catalogos\Update\UpdateCatTipoContactoRequest;
use App\Interfaces\Catalogos\CatTiposContactosRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatTipoContactoController extends Controller
{
    protected $_tipos;

    public function __construct(CatTiposContactosRepositoryInterface $tipos)
    {
        $this->_tipos = $tipos;
    }

    public function getAll()
    {
        try {
            $all = $this->_tipos->getAll();
            return ApiResponseHelper::sendResponse($all, 'Tipos de contacto obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $tipo = $this->_tipos->getByID($id);
            if (!$tipo) {
                return ApiResponseHelper::rollback(null, 'Tipo de contacto no encontrado', 404);
            }
            return ApiResponseHelper::sendResponse($tipo, 'Tipo de contacto obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatTipoContactoRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'clave',
                'descripcion',
                'habilitado']);
            $tipo = $this->_tipos->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse($tipo, 'Tipo de contacto creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateCatTipoContactoRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'clave',
                'descripcion',
                'habilitado']);
            $updated = $this->_tipos->update($data, $id);

            if (!$updated) {
                DB::rollBack();
                return ApiResponseHelper::rollback(null, 'Tipo de contacto no encontrado', 404);
            }

            DB::commit();
            return ApiResponseHelper::sendResponse($updated, 'Tipo de contacto actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
