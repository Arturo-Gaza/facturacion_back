<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Interfaces\SistemaFacturacion\ReversionRepositoryInterface;
use Exception;
use Illuminate\Http\Request;

class ReversionController extends Controller
{
    protected $reversionRepository;

    public function __construct(ReversionRepositoryInterface $reversionRepository)
    {
        $this->reversionRepository = $reversionRepository;
    }

    public function solicitar(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            $request->validate([
                'id_solicitud' => 'required|integer'
            ]);
            $id_solicitud = $request->input('id_solicitud');
            $rev = $this->reversionRepository->crearSolicitud($id_solicitud, $userId);

            return ApiResponseHelper::sendResponse($rev, 'Solicitud creada', 201);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, $ex->getMessage(), 500);
        }
    }

    public function generarToken(Request $request)
    {
        try {
            $adminId = auth()->user()->id;
            $request->validate([
                'id_solicitud' => 'required|integer'
            ]);
            $id_solicitud = $request->input('id_solicitud');
            $tokenPlain = $this->reversionRepository->generarToken($id_solicitud, $adminId, 5);

            return ApiResponseHelper::sendResponse([
                'token' => $tokenPlain,
                'nota' => 'El token se muestra solo una vez. Caduca en 5 minutos.'
            ], 'Token generado', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, $ex->getMessage(), 500);
        }
    }

    public function usarToken(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            $request->validate([
                'token' => 'required|string',
                'id_solicitud' => 'required|integer'
            ]);
            $tokenPlain = $request->input('token');
            $id_solicitud = $request->input('id_solicitud');
            $resultado = $this->reversionRepository->validarYUsarToken($id_solicitud, $tokenPlain, $userId);

            if (!isset($resultado['exito'])) {
                return ApiResponseHelper::rollback('Resultado inválido del repositorio', 500);
            }

            if ($resultado['exito']) {
                return ApiResponseHelper::sendResponse($resultado, 'Reversión ejecutada', 200);
            }

            return ApiResponseHelper::rollback($resultado['detalle'] ?? 'No se pudo ejecutar la reversión', 400);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, $ex->getMessage(), 500);
        }
    }

    public function rechazar(Request $request, $id)
    {
        try {
            $adminId = auth()->user()->id;
            $request->validate([
                'id_solicitud' => 'required|integer'
            ]);
            $id_solicitud = $request->input('id_solicitud');
            $motivo = $request->input('motivo',null);
            $rev = $this->reversionRepository->rechazar($id_solicitud, $adminId, $motivo);

            return ApiResponseHelper::sendResponse($rev, 'Solicitud rechazada', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, $ex->getMessage(), 500);
        }
    }
}
