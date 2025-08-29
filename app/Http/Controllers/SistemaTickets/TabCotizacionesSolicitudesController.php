<?php

namespace App\Http\Controllers\SistemaTickets;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SistemaTickets\Store\StoreTabCotizacionesDetalleRequest;
use App\Http\Requests\SistemaTickets\Store\StoreTabObesrvacionesDetalleRequest;
use App\Http\Requests\SistemaTickets\Update\UpdateTabCotizacionesDetalleRequest;
use App\Http\Requests\SistemaTickets\Update\UpdateTabObesrvacionesDetalleRequest;
use App\Interfaces\SistemaTickets\TabCotizacionesSolicitudRepositoryInterface;
use App\Models\SistemaTickets\TabCotizacionesSolicitudesDetalle;
use App\Models\SistemaTickets\TabSolicitud;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabCotizacionesSolicitudesController extends Controller
{
    protected $_tabCotizacionesSolicitudes;

    public function __construct(TabCotizacionesSolicitudRepositoryInterface $tabCotizacionesSolicitudes)
    {
        $this->_tabCotizacionesSolicitudes = $tabCotizacionesSolicitudes;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_tabCotizacionesSolicitudes->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Cotizaciones obtenidas', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_tabCotizacionesSolicitudes->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Cotización obtenida', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function getByIdDetalle($id)
    {
        try {
            $getById = $this->_tabCotizacionesSolicitudes->getByIdDetalle($id);
            return ApiResponseHelper::sendResponse($getById, 'Cotización obtenida', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $elementosAAgregar = collect($request)->map(function ($cat) {
                return [
                    'id_solicitud' => $cat['id_solicitud'],
                    'nombre_cotizacion' => $cat['nombre_cotizacion'],
                    'archivo_cotizacion' => $cat['archivo_cotizacion'],
                    'id_usuario' => $cat['id_usuario'],
                    'justificacion_general' => $cat['justificacion_general'],
                    'recomendada' => $cat['recomendada']
                ];
            });

            foreach ($elementosAAgregar as $elemento) {
                $this->_tabCotizacionesSolicitudes->store($elemento); // Ahora sí estás pasando un array
            }
            $idSolicitud = $elementosAAgregar->first()['id_solicitud'];
            $solicitud = TabSolicitud::find($idSolicitud);
            $solicitud->cotizadoGB = true;
            $solicitud->cotizacion_global = true;
            $solicitud->save();

            DB::commit();
            return ApiResponseHelper::sendResponse($solicitud, 'Cotización creada correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(Request $cat)
    {
        DB::beginTransaction();
        try {

            $elementosAActualizar = collect($cat)->map(function ($cat) {
                return [
                    'id' => $cat['id'],
                    'id_solicitud' => $cat['id_solicitud'],
                    'nombre_cotizacion' => $cat['nombre_cotizacion'],
                    //'archivo_cotizacion'=>$cat['archivo_cotizacion'],
                    'id_usuario' => $cat['id_usuario'],
                    'justificacion_general' => $cat['justificacion_general'],
                    'recomendada' => $cat['recomendada']
                ];
            });

            foreach ($elementosAActualizar as $elemento) {
                $this->_tabCotizacionesSolicitudes->update($elemento, $elemento["id"]); // Ahora sí estás pasando un array
            }
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Cotización actualizada correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {

            $elementosAEliminar = collect($request)->map(function ($item) {
                return [
                    'id' => $item['id'],
                ];
            });
            foreach ($elementosAEliminar as $elemento) {
                $this->_tabCotizacionesSolicitudes->delete($elemento['id']); // Ahora sí estás pasando un array
            }


            DB::commit();
            return ApiResponseHelper::sendResponse($elementosAEliminar, 'Registro eliminado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
