<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Interfaces\SistemaFacturacion\SuscripcionesRepositoryInterface;
use Exception;

class SuscripcionController extends Controller
{
    protected $suscripcionRepository;

    public function __construct(SuscripcionesRepositoryInterface $suscripcionRepository)
    {
        $this->suscripcionRepository = $suscripcionRepository;
    }

    public function getAll()
    {
        try {
            $all = $this->suscripcionRepository->getAll();
            return ApiResponseHelper::sendResponse($all, 'Suscripciones obtenidas', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }
    public function iniciar($id_plan)
    {
        try {
            $id_user = auth('sanctum')->id();
            $all = $this->suscripcionRepository->iniciar($id_user ,$id_plan);
            return ApiResponseHelper::sendResponse($all, 'Suscripción iniciada correctamente', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'Error al procesar la solicitud de suscripción :' .$ex->getMessage(), 500);
        }
    }


}
