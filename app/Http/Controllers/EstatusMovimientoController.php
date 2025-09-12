<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Interfaces\SistemaFacturacion\EstatusMovimientoRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstatusMovimientoController extends Controller
{
    protected $estatusMovimientoRepository;

    public function __construct(EstatusMovimientoRepositoryInterface $estatusMovimientoRepository)
    {
        $this->estatusMovimientoRepository = $estatusMovimientoRepository;
    }

    public function getAll()
    {
        try {
            $all = $this->estatusMovimientoRepository->getAll();
            return ApiResponseHelper::sendResponse($all, 'Estatus de movimiento obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $estatus = $this->estatusMovimientoRepository->getByID($id);
            return ApiResponseHelper::sendResponse($estatus, 'Estatus de movimiento obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only(['nombre', 'descripcion']);
            $estatus = $this->estatusMovimientoRepository->store($data);

            DB::commit();
            return ApiResponseHelper::sendResponse($estatus, 'Estatus de movimiento creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->only(['nombre', 'descripcion']);
            $updated = $this->estatusMovimientoRepository->update($data, $id);

            DB::commit();
            return ApiResponseHelper::sendResponse($updated, 'Estatus de movimiento actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}