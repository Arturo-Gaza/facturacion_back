<?php

namespace App\Http\Controllers\SistemaTickets;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SistemaTickets\Store\StoreTabObesrvacionesDetalleRequest;
use App\Http\Requests\SistemaTickets\Update\UpdateTabObesrvacionesDetalleRequest;
use App\Interfaces\SistemaTickets\TabObesrvacionesDetalleRepositoryInterface;
use App\Models\SistemaTickets\TabArchivosObservacionesDetalle;
use App\Models\SistemaTickets\TabSolicitud;
use App\Models\SistemaTickets\TabSolicitudDetalle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabObesrvacionesDetalleController extends Controller
{
    protected $_tabObservacionesDetalle;

    public function __construct(TabObesrvacionesDetalleRepositoryInterface $tabObservacionesDetalle)
    {
        $this->_tabObservacionesDetalle = $tabObservacionesDetalle;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_tabObservacionesDetalle->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_tabObservacionesDetalle->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function getByIdDetalle($id)
    {
        try {
            $getById = $this->_tabObservacionesDetalle->getByIdDetalle($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreTabObesrvacionesDetalleRequest $cat)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_solicitud_detalle' => $cat->id_solicitud_detalle,
                'id_usuario' => $cat->id_usuario,
                'observacion' => $cat->observacion
            ];
            $observacion = $this->_tabObservacionesDetalle->store($data);
            // Si vienen archivos adjuntos en el request
            if ($cat->has('archivos')) {
                foreach ($cat->archivos as $archivo) {
                    TabArchivosObservacionesDetalle::create([
                        'id_observacion_detalle' => $observacion->id,
                        'nombre' => $archivo['nombre'],
                        'archivo' => $archivo['archivo'],
                    ]);
                }
            }
            $solicitudD = TabSolicitudDetalle::find($cat->id_solicitud_detalle);
            $solicitud=TabSolicitud::find($solicitudD->id_solicitud);
            $solicitud->cotizacion_global = false;
             $solicitud->save();
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'observación creada correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateTabObesrvacionesDetalleRequest $cat, $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_solicitud_detalle' => $cat->id_solicitud_detalle,
                'id_usuario' => $cat->id_usuario,
                'observacion' => $cat->observacion
            ];
            $this->_tabObservacionesDetalle->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'observación actualizada correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
