<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Interfaces\SistemaFacturacion\SolicitudRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitudController extends Controller
{
    protected $solicitudRepository;

    public function __construct(SolicitudRepositoryInterface $solicitudRepository)
    {
        $this->solicitudRepository = $solicitudRepository;
    }

    public function getAll()
    {
        try {
            $all = $this->solicitudRepository->getAll();
            return ApiResponseHelper::sendResponse($all, 'Solicitudes obtenidas', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $solicitud = $this->solicitudRepository->getByID($id);
            return ApiResponseHelper::sendResponse($solicitud, 'Solicitud obtenida', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'usuario_id',
                'imagen_url',
                'texto_ocr',
                'estado',
                'empleado_id',
                'estado_id'
            ]);
            $solicitud = $this->solicitudRepository->store($data);

            DB::commit();
            return ApiResponseHelper::sendResponse($solicitud, 'Solicitud creada correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'usuario_id',
                'imagen_url',
                'texto_ocr',
                'estado',
                'empleado_id',
                'estado_id'
            ]);
            $updated = $this->solicitudRepository->update($data, $id);

            DB::commit();
            return ApiResponseHelper::sendResponse($updated, 'Solicitud actualizada correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}