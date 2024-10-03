<?php

namespace App\Http\Controllers\ArchivoCarga;

use App\Http\Controllers\Controller;
use App\Interfaces\ArchivoCarga\TabArchivoCargaRepositoryInterface;
use Illuminate\Http\Request;
use App\Classes\ApiResponseHelper;
use App\Http\Requests\ArchivoCarga\Store\StoreTabDetalleCargaRequest;
use App\Http\Requests\ArchivoCArga\Update\UpdateTabDetalleCargaRequest;
use Exception;
use Illuminate\Support\Facades\DB;

class TabDetalleCargaController extends Controller
{
    protected $_tabDetalleArchivo;

    public function __construct(TabArchivoCargaRepositoryInterface $tabDetalleArchivo)
    {
        $this->_tabDetalleArchivo = $tabDetalleArchivo;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_tabDetalleArchivo->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_tabDetalleArchivo->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreTabDetalleCargaRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = [
                'cve_carga' => $request->cve_carga,
                'conteo' => $request->conteo,
                'nombre_archivo' => $request->nombre_archivo,
                'id_usuario' => $request->id_usuario,
                'Reg_Archivo' => $request->Reg_Archivo,
                'Reg_a_Contar' => $request->Reg_a_Contar,
                'reg_vobo' => $request->reg_vobo,
                'reg_excluidos' => $request->reg_excluidos,
                'reg_incorpora' => $request->reg_incorpora,
                'id_estatus' => $request->id_estatus,
                'observaciones' => $request->observaciones,
                'habilitado' => $request->habilitado,
            ];

            $detalleCarga = $this->_tabDetalleArchivo->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Registro creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateTabDetalleCargaRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'cve_carga' => $request->cve_carga,
                'conteo' => $request->conteo,
                'nombre_archivo' => $request->nombre_archivo,
                'id_usuario' => $request->id_usuario,
                'Reg_Archivo' => $request->Reg_Archivo,
                'Reg_a_Contar' => $request->Reg_a_Contar,
                'reg_vobo' => $request->reg_vobo,
                'reg_excluidos' => $request->reg_excluidos,
                'reg_incorpora' => $request->reg_incorpora,
                'id_estatus' => $request->id_estatus,
                'observaciones' => $request->observaciones,
                'habilitado' => $request->habilitado,
            ];
            $this->_tabDetalleArchivo->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Catálogo actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }


    public function updateConte($id)
    {
        DB::beginTransaction();
        $conteo = DB::table('tab_detalle_cargas')
            ->where('id', $id)
            ->value('conteo') + 1;

        try {
            $data = [
                'conteo' => $conteo,
                'id_estatus' => 8,
            ];
            $this->_tabDetalleArchivo->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse($conteo, 'Catálogo actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function ValidarCierre()
    {
        $idsCargas = DB::table('tab_detalle_cargas')
            ->select(
                'id as id_carga'
            )->where('id_estatus', '!=', 3)
            ->where('id_estatus', '!=', 1) // omitimos el cargado
            ->where('id_estatus', '!=', 8) // omitimos el cargado
            ->get();

        $results = array();
        foreach ($idsCargas as $record) {

            $exists = DB::table('tab_asignacions')
                ->where('id_carga', $record->id_carga)
                ->where('id_estatus', '!=', 3)
                ->where('habilitado', true)
                ->exists();

            if (!$exists) {
                DB::beginTransaction();
                try {
                    $data = [
                        'id_estatus' => 7,
                    ];
                    $detalleCarga = $this->_tabDetalleArchivo->update($data, $record->id_carga);
                    DB::commit();
                    $results[] = $detalleCarga;
                    // return ApiResponseHelper::sendResponse($detalleCarga, 'Catálogo actualizado correctamente', 200);
                } catch (Exception $ex) {
                    DB::rollBack();
                    return ApiResponseHelper::rollback($ex);
                }
            }
        }
        return  $results;
    }

    public function ValidarCierreUsuarios($idCarga)
    {
        $results = DB::table('tab_asignacions')
            ->join('users', 'users.id', '=', 'tab_asignacions.id_usuario')
            ->join('cat_estatuses', 'cat_estatuses.id', '=', 'tab_asignacions.id_estatus')
            ->where('tab_asignacions.id_carga', $idCarga)
            ->where('tab_asignacions.id_estatus', '!=', 3)
            ->where('tab_asignacions.habilitado', true)
            ->select(
                'users.name',
                'users.apellidoP',
                'users.apellidoM',
                'users.email',
                'users.user',
                'tab_asignacions.id_estatus',
                'cat_estatuses.nombre as status_nombre'
            )
            ->get();

        return  $results;
    }

    public function deleteCarga($idCarga)
    {
        DB::beginTransaction();
        try {
            $deleteCarga = $this->_tabDetalleArchivo->deleteCarga($idCarga);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, $deleteCarga, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex,"Ocurrio un error al eliminar la carga.");
        }
    }

    public function validarEstatusCarga($idCarga,$conteo)
    {
        try {
            $validar = $this->_tabDetalleArchivo->validarEstatusCarga($idCarga,$conteo);
            return ApiResponseHelper::sendResponse(["numConteo"=> $validar], 'Núemro de conteos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }
}
