<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Classes\ApiResponseHelper;
use App\Http\Requests\ArchivoCarga\Store\StoreTabArchivoDetalleRequest;
use App\Http\Requests\SistemaTickets\Store\StoreSolicitudDetalleRequest;
use App\Http\Requests\SistemaTickets\Update\UpdateSolicitudDetalleRequest;
use App\Interfaces\TabArchivoSolicitudesDetalleRepositoryInterface;
use App\Models\SistemaTickets\TabSolicitudDetalle;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class TabArchivoSolicitudesDetalleController extends Controller
{
    protected $archivo;

    public function __construct(TabArchivoSolicitudesDetalleRepositoryInterface $archivo)
    {
        $this->archivo = $archivo;
    }

    public function getAll()
    {
        try {
            $getAll = $this->archivo->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Archivos obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex->getMessage(), 'No se pudo obtener la lista', 500);
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
                $this->archivo->delete($elemento['id']); // Ahora sí estás pasando un array
            }


            DB::commit();
            return ApiResponseHelper::sendResponse($elementosAEliminar, 'Registro eliminado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function getByID($id)
    {
        try {
            $getById = $this->archivo->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Archivos obtenida', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function getByIDSolicitudDeta($id)
    {
        try {
            $getById = $this->archivo->getByIDSolicitudDeta($id);
            return ApiResponseHelper::sendResponse($getById, 'Archivos obtenida', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }




    public function store(StoreTabArchivoDetalleRequest $request)
    {
        DB::beginTransaction();
        try {
           $archivos=$request->archivos;
           $id_solicitud_detalle=$request->id;

            $elementosAAgregar = collect($archivos)->map(function ($item) {
                return [
                    'id_solicitud_detalle' => $item['id_solicitud_detalle'],
                    'nombre' => $item['nombre'],
                    'archivo' => $item['archivo'],
                ];
            });

            foreach ($elementosAAgregar as $elemento) {
                $this->archivo->store($elemento); // Ahora sí estás pasando un array
            }
            $data2 = TabSolicitudDetalle::where('id_solicitud',$id_solicitud_detalle)->where('habilitado',true)->get();
            DB::commit();
            return ApiResponseHelper::sendResponse($elementosAAgregar, 'Registro creado correctamente', 201, $data2);
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
                'id_solicitud_detalle' => $request->id_solicitud_detalle,
                'id_usuario' => $request->id_usuario,
                'archivo' => $request->archivo,
            ];
            $this->archivo->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Registro actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
