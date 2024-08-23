<?php

namespace App\Http\Controllers\AsignacionCarga;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AsignacionCarga\Store\StoreTabAsignacion;
use App\Http\Requests\AsignacionCarga\Update\UpdateTabAsignacion;
use App\Interfaces\AsignacionCarga\TabAsignacionInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabAsignacionController extends Controller
{
    protected $_tabAsignacion;

    public function __construct(TabAsignacionInterface $tabAsignacion)
    {
        $this->_tabAsignacion = $tabAsignacion;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_tabAsignacion->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Asignacion obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_tabAsignacion->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Asignacion obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreTabAsignacion $tab)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_carga' => $tab->id_carga,
                'id_usuario' => $tab->id_usuario,
                'conteo' => $tab->conteo,
                'fecha_asignacion' => $tab->fecha_asignacion,
                'fecha_inicio_conteo' => $tab->fecha_inicio_conteo,
                'fecha_fin_conteo' => $tab->fecha_fin_conteo,
                'id_estatus' => $tab->id_estatus,
                'habilitado' => $tab->habilitado
            ];
            $asignacion = $this->_tabAsignacion->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Asigancion creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateTabAsignacion $tab, $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_carga' => $tab->id_carga,
                'id_usuario' => $tab->id_usuario,
                'conteo' => $tab->conteo,
                'fecha_asignacion' => $tab->fecha_asignacion,
                'fecha_inicio_conteo' => $tab->fecha_inicio_conteo,
                'fecha_fin_conteo' => $tab->fecha_fin_conteo,
                'id_estatus' => $tab->id_estatus,
                'habilitado' => $tab->habilitado
            ];
            $this->_tabAsignacion->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Asignacion actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
