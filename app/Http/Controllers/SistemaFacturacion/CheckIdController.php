<?php

namespace App\Http\Controllers\SistemaFacturacion;

use App\Services\CheckIdService;
use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class CheckIdController extends Controller
{
    protected $checkId;

    public function __construct(CheckIdService $checkId)
    {
        $this->checkId = $checkId;
    }

    public function buscar(Request $request)
    {

        try {

            $request->validate([
                'termino' => 'required|string'
            ]);

            $data = $this->checkId->buscar($request->termino);
            return ApiResponseHelper::sendResponse($data, 'Datos obtenidos correctamente', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, $ex->getMessage(), 500);
        }
    }
}
