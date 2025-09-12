<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Interfaces\SistemaFacturacion\EstadoSolicitudRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstadoSolicitudController extends Controller
{
    protected $estadoSolicitudRepository;

    public function __construct(EstadoSolicitudRepositoryInterface $estadoSolicitudRepository)
    {
        $this->estadoSolicitudRepository = $estadoSolicitudRepository;
    }

    public function getAll()
    {
        try {
            $all = $this->estadoSolicitudRepository->getAll();
            return ApiResponseHelper::sendResponse($all, 'Estados de solicitud obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $estado = $this->estadoSolicitudRepository->getByID($id);
            return ApiResponseHelper::sendResponse($estado, 'Estado de solicitud obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only(['nombre', 'descripcion']);
            $estado = $this->estadoSolicitudRepository->store($data);

            DB::commit();
            return ApiResponseHelper::sendResponse($estado, 'Estado de solicitud creado correctamente', 201);
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
            $updated = $this->estadoSolicitudRepository->update($data, $id);

            DB::commit();
            return ApiResponseHelper::sendResponse($updated, 'Estado de solicitud actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}