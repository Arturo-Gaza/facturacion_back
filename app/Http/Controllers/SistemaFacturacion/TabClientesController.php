<?php

namespace App\Http\Controllers\SistemaFacturacion;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SistemaFacturacion\Store\StoreTabClientesRequest;
use App\Http\Requests\SistemaFacturacion\Update\UpdateTabClientesRequest;
use App\Interfaces\SistemaFacturacion\TabClientesRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabClientesController extends Controller
{
protected $clientes;

    public function __construct(TabClientesRepositoryInterface $clientes)
    {
        $this->clientes = $clientes;
    }

    public function getAll()
    {
        try {
            $all = $this->clientes->getAll();
            return ApiResponseHelper::sendResponse($all, 'Clientes obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $cliente = $this->clientes->getByID($id);
            return ApiResponseHelper::sendResponse($cliente, 'Cliente obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreTabClientesRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'usuario',
                'password',
                'email',
                'habilitado']);
            $cliente = $this->clientes->store($data);

            DB::commit();
            return ApiResponseHelper::sendResponse($cliente, 'Cliente creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateTabClientesRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'usuario',
                'password',
                'email',
                'habilitado']);
            $updated = $this->clientes->update($data, $id);

            DB::commit();
            return ApiResponseHelper::sendResponse($updated, 'Cliente actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
