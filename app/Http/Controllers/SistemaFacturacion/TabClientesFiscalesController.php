<?php

namespace App\Http\Controllers\SistemaFacturacion;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SistemaFacturacion\Store\StoreTabClientesFiscalesRequest;
use App\Http\Requests\SistemaFacturacion\Update\UpdateTabClientesFiscalesRequest;
use App\Http\Requests\SistemaFacturacion\Update\UpdateTabClientesRequest;
use App\Interfaces\SistemaFacturacion\TabClientesFiscalesRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class TabClientesFiscalesController extends Controller
{
    protected $_clientesFiscales;

    public function __construct(TabClientesFiscalesRepositoryInterface $clientesFiscales)
    {
        $this->_clientesFiscales = $clientesFiscales;
    }

    public function getAll()
    {
        try {
            $all = $this->_clientesFiscales->getAll();
            return ApiResponseHelper::sendResponse($all, 'Clientes fiscales obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $cliente = $this->_clientesFiscales->getByID($id);
            if (!$cliente) {
                return ApiResponseHelper::rollback(null, 'Cliente no encontrado', 404);
            }
            return ApiResponseHelper::sendResponse($cliente, 'Cliente obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreTabClientesFiscalesRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'id_cliente',
                'nombre_razon',
                'nombre_comercial',
                'es_persona_moral',
                'rfc',
                'curp',
                'id_regimen',
                'fecha_inicio_op',
                'id_estatus_sat',
                'datos_extra'
            ]);

            $fiscal = $this->_clientesFiscales->store($data);

            DB::commit();
            return ApiResponseHelper::sendResponse($fiscal, 'Cliente fiscal creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
public function update(UpdateTabClientesFiscalesRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'id_cliente', 'nombre_razon', 'nombre_comercial',
                'es_persona_moral', 'rfc', 'curp', 'id_regimen',
                'fecha_inicio_op', 'id_estatus_sat', 'datos_extra'
            ]);

            $updated = $this->_clientesFiscales->update($data, $id);

            if (!$updated) {
                DB::rollBack();
                return ApiResponseHelper::rollback(null, 'Cliente fiscal no encontrado', 404);
            }

            DB::commit();
            return ApiResponseHelper::sendResponse($updated, 'Cliente fiscal actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
