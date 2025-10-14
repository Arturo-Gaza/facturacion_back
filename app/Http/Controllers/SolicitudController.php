<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Interfaces\SistemaFacturacion\SolicitudRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

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


    public function getConsola()
    {
        try {
            $idUsr = auth()->user()->id;
            $all = $this->solicitudRepository->getConsola($idUsr);
            return ApiResponseHelper::sendResponse($all, 'Solicitudes obtenidas', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, $ex->getMessage(), 500);
        }
    }

    public function getMesaAyuda()
    {
        try {
            $idUsr = auth()->user()->id;
            $all = $this->solicitudRepository->getMesaAyuda();
            return ApiResponseHelper::sendResponse($all, 'Usuarios obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, $ex->getMessage(), 500);
        }
    }
    public function procesar(int $id)
    {
        try {
            $all = $this->solicitudRepository->procesar($id);
            return ApiResponseHelper::sendResponse($all, 'Solicitudes obtenidas', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }
    public function enviar(int $id)
    {
        try {
            $id_user = auth('sanctum')->id();
            $all = $this->solicitudRepository->enviar($id, $id_user);
            return ApiResponseHelper::sendResponse($all, 'Solicitudes obtenidas', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }
    public function asignar(Request $request)
    {
        try {
            $id_user = auth('sanctum')->id();
            $request->validate([
                'id_solicitud' => 'required|integer',
                'id_empleado' => 'required|integer'
            ]);

            $id_solicitud = $request->input('id_solicitud');
            $id_empleado = $request->input('id_empleado');
            $all = $this->solicitudRepository->asignar($id_user, $id_solicitud, $id_empleado);
            return ApiResponseHelper::sendResponse($all, 'Solicitudes obtenidas', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function actualizarReceptor(Request $request)
    {
        try {
            $all = $this->solicitudRepository->actualizarReceptor($request);
            return ApiResponseHelper::sendResponse($all, 'Solicitudes actualizada con exito', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function eliminar(int $id)
    {
        try {
            $all = $this->solicitudRepository->eliminar($id);
            return ApiResponseHelper::sendResponse($all, 'Ticket eliminado con éxito', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }
    public function obtenerImagen(int $id)
    {
        try {
            $all = $this->solicitudRepository->obtenerImagen($id);
            return ApiResponseHelper::sendResponse($all, 'Imagen obtenida', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }


    public function getByUsuario(int $usuario_id)
    {
        try {
            $all = $this->solicitudRepository->getByUsuario($usuario_id);
            return ApiResponseHelper::sendResponse($all, 'Solicitudes obtenidas', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }
    public function getGeneralByUsuario(int $usuario_id)
    {
        try {
            $all = $this->solicitudRepository->getGeneralByUsuario($usuario_id);
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
            $id_user = auth('sanctum')->id();
            $solicitud = $this->solicitudRepository->store($request, $id_user);

            DB::commit();
            return ApiResponseHelper::sendResponse($solicitud, 'Solicitud creada correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex, $ex->getMessage());
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
