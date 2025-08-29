<?php

namespace App\Http\Controllers\SistemaTickets;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SistemaTickets\Store\StoreTabObesrvacionesSolReqInfoRequest;
use App\Http\Requests\SistemaTickets\Update\UpdateTabObesrvacionesSolReqInfoRequest;
use App\Interfaces\SistemaTickets\TabObservacionesSolicitudReqInfoRepositoryInterface;
use App\Models\SistemaTickets\TabArchivosObservacionesSolicitudReqInfo;
use App\Models\SistemaTickets\TabSolicitud;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabObservacionesSolicitudReqInfoController extends Controller
{
    protected $_tabObservacionesSolReqInfo;

    public function __construct(TabObservacionesSolicitudReqInfoRepositoryInterface $tabObservacionesDetalle)
    {
        $this->_tabObservacionesSolReqInfo = $tabObservacionesDetalle;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_tabObservacionesSolReqInfo->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_tabObservacionesSolReqInfo->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function getByIdSolicitud($id)
    {
        try {
            $getById = $this->_tabObservacionesSolReqInfo->getByIdSolicitud($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreTabObesrvacionesSolReqInfoRequest $cat)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_solicitud' => $cat->id_solicitud,
                'id_usuario' => $cat->id_usuario,
                'observacion' => $cat->observacion
            ];
            $observacion = $this->_tabObservacionesSolReqInfo->store($data);
            // Si vienen archivos adjuntos en el request
            if ($cat->has('archivos')) {
                foreach ($cat->archivos as $archivo) {
                    TabArchivosObservacionesSolicitudReqInfo::create([
                        'id_observacion_solicitud_req_info' => $observacion->id,
                        'nombre' => $archivo['nombre'],
                        'archivo' => $archivo['archivo'],
                    ]);
                }
            }

            $idSolicitud = $cat->id_solicitud;
            $solicitud = TabSolicitud::find($idSolicitud);
            $solicitud->cotizacion_global = true;
            $solicitud->cotizadoGB = false;
            $solicitud->save();


            DB::commit();
            return ApiResponseHelper::sendResponse( $solicitud, 'observación creada correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateTabObesrvacionesSolReqInfoRequest  $cat, $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_solicitud' => $cat->id_solicitud,
                'id_usuario' => $cat->id_usuario,
                'observacion' => $cat->observacion
            ];
            $this->_tabObservacionesSolReqInfo->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'observación actualizada correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
