<?php

namespace App\Http\Controllers\SistemaTickets;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Interfaces\SistemaTickets\TabArchivosObservacionesDetalleRepositoryInterface;
use App\Interfaces\SistemaTickets\TabArchivosObservacionesSolicitudReqInfoRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabArchivosObservacionesSolicitudReqInfoController extends Controller
{
    protected $_archivoRepo;

    public function __construct(TabArchivosObservacionesSolicitudReqInfoRepositoryInterface $archivoRepo)
    {
        $this->_archivoRepo = $archivoRepo;
    }

    public function getAll()
    {
        try {
            $result = $this->_archivoRepo->getAll();
            return ApiResponseHelper::sendResponse($result, 'Listado obtenido correctamente', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'Error al obtener el listado', 500);
        }
    }

    public function getById($id)
    {
        try {
            $result = $this->_archivoRepo->getByID($id);
            return ApiResponseHelper::sendResponse($result, 'Registro obtenido correctamente', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'Error al obtener el registro', 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_observacion_solicitud_req_info' => $request->id_observacion_solicitud_req_info,
                'nombre' => $request->nombre,
                'archivo' => $request->archivo, // Asegúrate de procesar archivos si usas upload
            ];

            $this->_archivoRepo->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Archivo guardado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_observacion_solicitud_req_info' => $request->id_observacion_solicitud_req_info,
                'nombre' => $request->nombre,
                'archivo' => $request->archivo,
            ];

            $this->_archivoRepo->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Archivo actualizado correctamente', 200);
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
                $this->_archivoRepo->delete($elemento['id']); // Ahora sí estás pasando un array
            }


            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Archivo eliminado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
