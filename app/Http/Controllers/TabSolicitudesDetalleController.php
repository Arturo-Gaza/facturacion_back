<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Classes\ApiResponseHelper;
use App\Http\Requests\ArchivoCarga\Store\StoreTabSolicitudesRequest;
use App\Http\Requests\ArchivoCarga\Update\UpdateTabSolicitudesRequest;
use App\Http\Requests\SistemaTickets\Store\StoreSolicitudDetalleRequest;
use App\Http\Requests\SistemaTickets\Update\UpdateSolicitudDetalleRequest;
use App\Interfaces\TabSolicitudesDetalleRepositoryInterface;
use App\Interfaces\TabSolicitudesRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class TabSolicitudesDetalleController extends Controller
{
    protected $tabSolicitudes;

    public function __construct(TabSolicitudesDetalleRepositoryInterface $tabSolicitudes)
    {
        $this->tabSolicitudes = $tabSolicitudes;
    }

    public function getAll()
    {
        try {
            $getAll = $this->tabSolicitudes->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Solicitudes obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex->getMessage(), 'No se pudo obtener la lista', 500);
        }
    }

    public function getByID($id)
    {
        try {
            $getById = $this->tabSolicitudes->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Solicitud obtenida', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function getByIDSolicitud($id)
    {
        try {
            $getById = $this->tabSolicitudes->getByIDSolicitud($id);
            return ApiResponseHelper::sendResponse($getById, 'Solicitud obtenida', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }




    public function store(StoreSolicitudDetalleRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_producto' => $request->id_producto,
                'id_solicitud' => $request->id_solicitud,
                'descripcion' => $request->descripcion,
                'marca' => $request->marca,
                'modelo' => $request->modelo,
                'cantidad' => $request->cantidad,
                'observacion' => $request->observacion,
                'cotizado' => $request->cotizado
            ];

            $data = $this->tabSolicitudes->store($data);
            $data2=$this->tabSolicitudes->getByIDSolicitud($request->id_solicitud);
            DB::commit();
            return ApiResponseHelper::sendResponse($data, 'Registro creado correctamente', 201,$data2);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateSolicitudDetalleRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_producto' => $request->id_producto,
                'id_solicitud' => $request->id_solicitud,
                'descripcion' => $request->descripcion,
                'marca' => $request->marca,
                'modelo' => $request->modelo,
                'cantidad' => $request->cantidad,
                'observacion' => $request->observacion,
                'habilitado'=> $request->habilitado,
                'cotizado' => $request->cotizado
            ];
            $this->tabSolicitudes->update($data, $id);
            $data2=$this->tabSolicitudes->getByIDSolicitud($request->id_solicitud);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Registro actualizado correctamente', 200, $data2);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

        public function deleteByDetalle($id)
    {
        try {
            $getById = $this->tabSolicitudes->deleteByDetalle($id);
            $id_solicitud=$getById->id_solicitud;
             $data2=$this->tabSolicitudes->getByIDSolicitud($id_solicitud);
            return ApiResponseHelper::sendResponse($getById, 'Solicitud detalle eliminada', 200,$data2);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

            public function deleteBySolicitud($id)
    {
        try {
            $getById = $this->tabSolicitudes->deleteBySolicitud($id);
            return ApiResponseHelper::sendResponse($getById, 'Solicitud detalle eliminadas', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }
}
