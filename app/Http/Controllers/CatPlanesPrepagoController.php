<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Interfaces\CatPlanesPrepagoRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatPlanesPrepagoController extends Controller
{
    protected $catPlanesPrepagoRepository;

    public function __construct(CatPlanesPrepagoRepositoryInterface $catPlanesPrepagoRepository)
    {
        $this->catPlanesPrepagoRepository = $catPlanesPrepagoRepository;
    }

    public function getAll()
    {
        try {
            $planes = $this->catPlanesPrepagoRepository->getAll();
            return ApiResponseHelper::sendResponse($planes, 'Planes prepago obtenidos correctamente', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista de planes prepago', 500);
        }
    }

    public function getById($id)
    {
        try {
            $plan = $this->catPlanesPrepagoRepository->getById($id);
            
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
                'creditos',
                'monto',
                'activo'
            ]);

            $plan = $this->catPlanesPrepagoRepository->store($data);

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
                'creditos',
                'monto',
                'activo'
            ]);

            $plan = $this->catPlanesPrepagoRepository->update($data, $id);

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

    public function activate($id)
    {
        DB::beginTransaction();
        try {
            $plan = $this->catPlanesPrepagoRepository->activate($id);

            if (!$plan) {
                return ApiResponseHelper::sendResponse(null, 'Plan no encontrado', 404);
            }

            DB::commit();
            return ApiResponseHelper::sendResponse($plan, 'Plan activado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function deactivate($id)
    {
        DB::beginTransaction();
        try {
            $plan = $this->catPlanesPrepagoRepository->deactivate($id);

            if (!$plan) {
                return ApiResponseHelper::sendResponse(null, 'Plan no encontrado', 404);
            }

            DB::commit();
            return ApiResponseHelper::sendResponse($plan, 'Plan desactivado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}