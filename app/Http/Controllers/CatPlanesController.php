<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Interfaces\SistemaFacturacion\CatPlanesRepositoryInterface ;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatPlanesController extends Controller
{
    protected $catPlanesRepository;

    public function __construct(CatPlanesRepositoryInterface $catPlanesRepository)
    {
        $this->catPlanesRepository = $catPlanesRepository;
    }

    public function getAll()
    {
        try {
            $planes = $this->catPlanesRepository->getAll();
            return ApiResponseHelper::sendResponse($planes, 'Planes obtenidos correctamente', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista de planes', 500);
        }
    }

        public function getAllVigentes()
    {
        try {
            $planes = $this->catPlanesRepository->getAllVigentes();
            return ApiResponseHelper::sendResponse($planes, 'Planes obtenidos correctamente', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista de planes', 500);
        }
    }

    public function getById($id)
    {
        try {
            $plan = $this->catPlanesRepository->getById($id);
            
            if (!$plan) {
                return ApiResponseHelper::sendResponse(null, 'Plan no encontrado', 404);
            }
            
            return ApiResponseHelper::sendResponse($plan, 'Plan obtenido correctamente', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el plan', 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'nombre',
                'descripcion',
                'precio',
                'duracion_dias',
                'creditos_incluidos',
                'activo'
            ]);

            $plan = $this->catPlanesRepository->store($data);

            DB::commit();
            return ApiResponseHelper::sendResponse($plan, 'Plan creado correctamente', 201);
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
                'nombre',
                'descripcion',
                'precio',
                'duracion_dias',
                'creditos_incluidos',
                'activo'
            ]);

            $plan = $this->catPlanesRepository->update($data, $id);

            if (!$plan) {
                return ApiResponseHelper::sendResponse(null, 'Plan no encontrado', 404);
            }

            DB::commit();
            return ApiResponseHelper::sendResponse($plan, 'Plan actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    
}