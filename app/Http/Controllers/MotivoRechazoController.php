<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Interfaces\SistemaFacturacion\MotivoRechazoRepositoryInterface;
 
use Exception;

class MotivoRechazoController extends Controller
{
    protected $solicitudRepository;

    public function __construct(MotivoRechazoRepositoryInterface $solicitudRepository)
    {
        $this->solicitudRepository = $solicitudRepository;
    }

    public function getAllActivo()
    {
        try {
            $all = $this->solicitudRepository->getAllActivo();
            return ApiResponseHelper::sendResponse($all, 'Solicitudes obtenidas', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    
}
