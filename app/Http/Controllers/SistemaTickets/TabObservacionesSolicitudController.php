<?php

namespace App\Http\Controllers\SistemaTickets;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SistemaTickets\Store\StoreTabObservacionesSolicitudRequest;
use App\Http\Requests\SistemaTickets\Update\UpdateTabObservacionesSolicitudRequest;
use App\Interfaces\SistemaTickets\TabObesrvacionesSolicitudRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabObservacionesSolicitudController extends Controller
{
    protected $_tabObservacionesSolicitud;

    public function __construct(TabObesrvacionesSolicitudRepositoryInterface $tabObservacionesSolicitud)
    {
        $this->_tabObservacionesSolicitud = $tabObservacionesSolicitud;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_tabObservacionesSolicitud->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_tabObservacionesSolicitud->getByID($id);
            return ApiResponseHelper::sendResponse([$getById], 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }
     public function getBySolicitud($id)
    {
        try {
            $obs = $this->_tabObservacionesSolicitud->getBySolicitudID($id);
            return ApiResponseHelper::sendResponse($obs, 'Observaciones obtenidas', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'Error al obtener observaciones', 500);
        }
    }

    public function store(StoreTabObservacionesSolicitudRequest $cat)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_solicitud' => $cat->id_solicitud,
                'id_usuario' => $cat->id_usuario,
                'observacion' => $cat->observacion
            ];
            $almacen = $this->_tabObservacionesSolicitud->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'observacion creada correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateTabObservacionesSolicitudRequest $cat, $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_solicitud' => $cat->id_solicitud,
                'id_usuario' => $cat->id_usuario,
                'observacion' => $cat->observacion
            ];
            $this->_tabObservacionesSolicitud->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'observacion actualizada correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
