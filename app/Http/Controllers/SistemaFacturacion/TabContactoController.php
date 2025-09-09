<?php

namespace App\Http\Controllers\SistemaFacturacion;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SistemaFacturacion\Store\StoreTabContactoRequest;
use App\Http\Requests\SistemaFacturacion\Update\UpdateTabContactoRequest;
use App\Interfaces\SistemaFacturacion\TabContactosRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabContactoController extends Controller
{
    protected $_contactos;

    public function __construct(TabContactosRepositoryInterface $contactos)
    {
        $this->_contactos = $contactos;
    }

    public function getAll()
    {
        try {
            $all = $this->_contactos->getAll();
            return ApiResponseHelper::sendResponse($all, 'Contactos obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $contacto = $this->_contactos->getByID($id);
            if (!$contacto) {
                return ApiResponseHelper::rollback(null, 'Contacto no encontrado', 404);
            }
            return ApiResponseHelper::sendResponse($contacto, 'Contacto obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreTabContactoRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'id_cliente',
                'id_tipo_contacto',
                'lada',
                'valor',
                'principal'
            ]);
            $contacto = $this->_contactos->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse($contacto, 'Contacto creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateTabContactoRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'id_cliente',
                'id_tipo_contacto',
                'lada',
                'valor',
                'principal'
            ]);
            $updated = $this->_contactos->update($data, $id);

            if (!$updated) {
                DB::rollBack();
                return ApiResponseHelper::rollback(null, 'Contacto no encontrado', 404);
            }

            DB::commit();
            return ApiResponseHelper::sendResponse($updated, 'Contacto actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
