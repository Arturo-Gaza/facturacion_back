<?php

namespace App\Http\Controllers\ArchivoCarga;

use App\Http\Controllers\Controller;
use App\Interfaces\ArchivoCarga\TabObservacionesRepositoryInterface;
use App\Classes\ApiResponseHelper;
use App\Http\Requests\ArchivoCarga\Store\StoreTabObservacionesRequest;
use App\Http\Requests\ArchivoCarga\Update\UpdateTabObservacionesRequest;
use Exception;
use Illuminate\Support\Facades\DB;


class TabObservacionesController extends Controller
{
    protected $_tabObservaciones;

    public function __construct(TabObservacionesRepositoryInterface $tabObservaciones)
    {
        $this->_tabObservaciones = $tabObservaciones;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_tabObservaciones->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_tabObservaciones->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function getByIDCarga($idCarga)
    {
        try {
            $getByIDCarga = $this->_tabObservaciones->getByIDCarga($idCarga);
            return ApiResponseHelper::sendResponse($getByIDCarga, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function getByIDCargaIDUser($idCarga, $idUser)
    {
        try {
            $getByIDCarga = $this->_tabObservaciones->getByIDCargaIDUser($idCarga, $idUser);
            return ApiResponseHelper::sendResponse($getByIDCarga, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreTabObservacionesRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_usuario' => $request->id_usuario,
                'id_detalle_carga' => $request->id_detalle_carga,
                'observacion' => $request->observacion,
                'habilitado' => $request->habilitado,
            ];

            $Observaciones = $this->_tabObservaciones->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Registro creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateTabObservacionesRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_usuario' => $request->id_usuario,
                'id_detalle_carga' => $request->id_detalle_carga,
                'observacion' => $request->observacion,
                'habilitado' => $request->habilitado,
            ];
            $this->_tabObservaciones->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
