<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Interfaces\SistemaFacturacion\ServicioRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServicioController extends Controller
{
    protected $servicioRepository;

    public function __construct(ServicioRepositoryInterface $servicioRepository)
    {
        $this->servicioRepository = $servicioRepository;
    }

    public function getAll()
    {
        try {
            $all = $this->servicioRepository->getAll();
            return ApiResponseHelper::sendResponse($all, 'Servicios obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $servicio = $this->servicioRepository->getByID($id);
            return ApiResponseHelper::sendResponse($servicio, 'Servicio obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only(['nombre', 'descripcion', 'activo']);
            $servicio = $this->servicioRepository->store($data);

            DB::commit();
            return ApiResponseHelper::sendResponse($servicio, 'Servicio creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->only(['nombre', 'descripcion', 'activo']);
            $updated = $this->servicioRepository->update($data, $id);

            DB::commit();
            return ApiResponseHelper::sendResponse($updated, 'Servicio actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}