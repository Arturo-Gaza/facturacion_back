<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Classes\ApiResponseHelper;
use App\Http\Requests\ArchivoCarga\Store\StoreTabSolicitudesRequest;
use App\Http\Requests\ArchivoCarga\Update\UpdateTabSolicitudesRequest;
use App\Interfaces\TabSolicitudesRepositoryInterface;
use App\Models\SistemaTickets\TabSolicitud;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabSolicitudesController extends Controller
{
    protected $tabSolicitudes;

    public function __construct(TabSolicitudesRepositoryInterface $tabSolicitudes)
    {
        $this->tabSolicitudes = $tabSolicitudes;
    }

    public function getAll()
    {
        try {
            $id = auth()->user()->id;
            $getAll = $this->tabSolicitudes->getAll($id);
            return ApiResponseHelper::sendResponse($getAll, 'Solicitudes obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex->getMessage(), 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->tabSolicitudes->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Solicitud obtenida', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function asignar(Request $request)
    {
        try {

            $data = [
                'id_usuario_que_asigna' => auth()->user()->id,

                'id_solicitud' => $request->id_solicitud,
            ];
            $data = $this->tabSolicitudes->asignar($data);
            return ApiResponseHelper::sendResponse($data, 'Solicitud asignada', 200);
        } catch (ModelNotFoundException $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo encontrar la solicitud', 500);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo asignar la solicitud', 500);
        }
    }
    public function reasignar(Request $request)
    {
        try {

            $data = [
                'id_usuario_que_asigna' => $request->id_usuario,

                'id_solicitud' => $request->id_solicitud,
            ];
            $data = $this->tabSolicitudes->reasignar($data);
            return ApiResponseHelper::sendResponse($data, 'Solicitud asignada', 200);
        } catch (ModelNotFoundException $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo encontrar la solicitud', 500);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo asignar la solicitud', 500);
        }
    }
    public function cambiarEstatus(Request $request)
    {
        try {

            $data = [
                'id_usuario_que_cambia' => auth()->user()->id,
                'id_estatus' => $request->id_estatus,
                'id_solicitud' => $request->id_solicitud,
            ];
            $data = $this->tabSolicitudes->cambiarEstatus($data);
            return ApiResponseHelper::sendResponse($data, 'Cambio de estatus de solicitud exitosa', 200);
        } catch (ModelNotFoundException $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo encontrar la solicitud', 500);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo cambiar el estatus de la solicitud', 500);
        }
    }




    public function store(StoreTabSolicitudesRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_usuario_solicitud' => auth()->user()->id,
                'descripcion' => $request->descripcion,
                'justificacion' => $request->justificacion,
                'prioridad' => $request->prioridad,
                'id_categoria' => $request->id_categoria,
                'justificacion_prioridad' => $request->justificacion_prioridad,
                'cotizacion_global' => $request->cotizacion_global
            ];

            $data = $this->tabSolicitudes->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse($data, 'Registro creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateTabSolicitudesRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'descripcion' => $request->descripcion,
                'justificacion' => $request->justificacion,
                'prioridad' => $request->prioridad,
                'id_categoria' => $request->id_categoria,
                'justificacion_prioridad' => $request->justificacion_prioridad,
                'cotizacion_global' => $request->cotizacion_global,
                'prioridadModificada' => $request->prioridadModificada,
                'cotizadoGB' => $request->cotizadoGB
            ];
            $this->tabSolicitudes->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Registro actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
    public function reporte(Request $request)
    {
        DB::beginTransaction();
        // Obtener el contenido de la solicitud como JSON
        $solicitudesJson = $request->getContent();

        // Decodificar el JSON a un array de PHP
        $solicitudesData = json_decode($solicitudesJson, true); // true para array asociativo

        // Verificar si la decodificación fue exitosa
        if ($solicitudesData === null) {
            return null;
        }

        // Extraer todos los 'id_solicitud' del array
        $ids = array_column($solicitudesData["datos"], 'id');
        $filtros = $solicitudesData["filtros"];
        try {
            $archivo = $this->tabSolicitudes->reporte($ids, $filtros);
            DB::commit();
            return ApiResponseHelper::sendResponse($archivo, 'Reporte obtenido correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
    public function formatearSolicitud($id)
    {
        try {
            $getAll = $this->tabSolicitudes->formatearSolicitud($id);
            return ApiResponseHelper::sendResponse($getAll, 'Solicitudes obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex->getMessage(), 'No se pudo obtener la lista', 500);
        }
    }

    public function getCotizaciones($id)
    {
        try {

            $archivos = $this->tabSolicitudes->getCotizaciones($id);
            return $archivos;
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex->getMessage(), 'No se pudo obtener los archivos', 500);
        }
    }
}
