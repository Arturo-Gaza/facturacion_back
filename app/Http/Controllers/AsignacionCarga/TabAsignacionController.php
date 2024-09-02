<?php

namespace App\Http\Controllers\AsignacionCarga;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AsignacionCarga\Store\StoreTabAsignacion;
use App\Http\Requests\AsignacionCarga\Update\UpdateTabAsignacion;
use App\Interfaces\ArchivoConteo\TabConteoRepositoryInterface;
use App\Interfaces\AsignacionCarga\TabAsignacionInterface;
use App\Models\ArchivoConteo\TabConteo;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabAsignacionController extends Controller
{
    protected $_tabAsignacion;
    protected $_tabConteon;

    public function __construct(TabAsignacionInterface $tabAsignacion, TabConteoRepositoryInterface $tabConteon)
    {
        $this->_tabAsignacion = $tabAsignacion;
        $this->_tabConteon = $tabConteon;
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

    public function getByIdCargaIdUserPer($idCarga, $idUser)
    {
        try {
            $getById = $this->_tabAsignacion->getByIdCargaIdUserPer($idCarga, $idUser);
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


    public function Asignacion(Request $Asignacion)
    {
        $idCarga = $Asignacion->id_carga;
        $idUsuario = $Asignacion->id_usuario;

        $existente = DB::table('tab_asignacions')->where('id_carga', $idCarga)
            ->where('id_usuario', $idUsuario)
            ->where('habilitado', 0)
            ->exists();

        if (!$existente) {
            DB::beginTransaction();
            try {
                $data = [
                    'id_carga' => $Asignacion->id_carga,
                    'id_usuario' => $Asignacion->id_usuario,
                    'conteo' => $Asignacion->conteo,
                    'fecha_asignacion' => Carbon::now(),
                    'fecha_inicio_conteo' => null,
                    'fecha_fin_conteo' => null,
                    'id_estatus' => 6,
                    'habilitado' => $Asignacion->habilitado
                ];
                $asignacion = $this->_tabAsignacion->store($data);
                DB::commit();
                return ApiResponseHelper::sendResponse(null, 'Asigancion creado correctamente', 201);
            } catch (Exception $ex) {
                DB::rollBack();
                return ApiResponseHelper::rollback($ex);
            }
        } else {
            $requesAsig = $this->_tabAsignacion->getByIdCargaIdUser($idCarga, $idUsuario);
            DB::beginTransaction();
            try {
                $data = [
                    'fecha_inicio_conteo' => null,
                    'fecha_fin_conteo' => null,
                    'id_estatus' => 6,
                    'habilitado' => $Asignacion->habilitado
                ];
                $this->_tabAsignacion->update($data, $requesAsig->id);
                DB::commit();
                return ApiResponseHelper::sendResponse(null, 'Asignacion actualizado correctamente', 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return ApiResponseHelper::rollback($ex);
            }

            // IMPORTANTE REVISAR SI SE VA A ELIMINAR LOS REGISTROS QUE YA SE TENIA EN EL CONTEO
            // return response()->json(['message' => 'El usuario ya existe se procede a actualizar solo el campo habilitado', 'data' => $requesAsig->id], 422);
        }
    }

    public function Designacion(Request $Asignacion, $idUserDesig)
    {
        $idCarga = $Asignacion->id_carga;
        $idUsuario = $Asignacion->id_usuario;

        $existente = DB::table('tab_asignacions')->where('id_carga', $idCarga)
            ->where('id_usuario', $idUsuario)
            ->where('habilitado', 0)
            ->exists();

        if (!$existente) {
            DB::beginTransaction();
            try {
                $data = [
                    'id_carga' => $Asignacion->id_carga,
                    'id_usuario' => $Asignacion->id_usuario,
                    'conteo' => $Asignacion->conteo,
                    'fecha_asignacion' => Carbon::now(),
                    'fecha_inicio_conteo' => null,
                    'fecha_fin_conteo' => null,
                    'id_estatus' => 6,
                    'habilitado' => $Asignacion->habilitado
                ];
                $asignacion = $this->_tabAsignacion->store($data);
                $this->DesignacionUserHabilitado($idCarga, $idUserDesig);
                $this->DuplicarConteo($idCarga, $idUsuario, $idUserDesig);
                //DUPLICARA LOS DATOS DEL CONTEO DEL VIEJO ASIGNADO ***
                DB::commit();
                return ApiResponseHelper::sendResponse(null, 'Asigancion creado correctamente', 201);
            } catch (Exception $ex) {
                DB::rollBack();
                return ApiResponseHelper::rollback($ex);
            }
        } else {
            $requesAsig = $this->_tabAsignacion->getByIdCargaIdUser($idCarga, $idUsuario);
            DB::beginTransaction();
            try {
                $data = [
                    'fecha_asignacion' => Carbon::now(),
                    'fecha_inicio_conteo' => null,
                    'fecha_fin_conteo' => null,
                    'id_estatus' => 6,
                    'habilitado' => true,
                ];
                $this->_tabAsignacion->update($data, $requesAsig->id);
                $this->DesignacionUserHabilitado($idCarga, $idUserDesig);
                $this->DuplicarConteo($idCarga, $idUsuario, $idUserDesig);
                DB::commit();
                return ApiResponseHelper::sendResponse(null, 'Asignacion actualizado correctamente', 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return ApiResponseHelper::rollback($ex);
            }

            // IMPORTANTE REVISAR SI SE VA A ELIMINAR LOS REGISTROS QUE YA SE TENIA EN EL CONTEO
            // return response()->json(['message' => 'El usuario ya existe se procede a actualizar solo el campo habilitado', 'data' => $requesAsig->id], 422);
        }
    }

    public function DesignacionUserHabilitado($idCarga, $idUser)
    {
        $requesAsigDes = $this->_tabAsignacion->getByIdCargaIdUser($idCarga, $idUser);
        DB::beginTransaction();
        try {
            $data = [
                'fecha_inicio_conteo' => Carbon::now(),
                'fecha_fin_conteo' => Carbon::now(),
                'id_estatus' => 4,
                'habilitado' => false
            ];
            $this->_tabAsignacion->update($data, $requesAsigDes->id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Asignacion actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function DuplicarConteo($idCarga, $idUser, $idUserDesig) // este solo se va a ejecutar en la api desig
    {
        $getallconteo = DB::table('tab_conteo')->where('id_carga', $idCarga)
            ->where('id_usuario', $idUserDesig)
            ->get();

        foreach ($getallconteo as $record) {
            DB::table('tab_conteo')->insert([
                "id_carga" => $idCarga,
                "id_usuario" => $idUser,
                "id_almacen" => $record->id_almacen,
                "id_unidadmedida" => $record->id_unidadmedida,
                "id_grupofamilia" => $record->id_grupofamilia,
                "id_producto" => $record->id_producto,
                "codigo" => $record->codigo,
                "descripcion" => $record->descripcion,
                "ume" => $record->ume,
                "cantidad" => $record->cantidad,
                "ubicacion" => $record->ubicacion,
                "observaciones" => $record->observaciones,
                "habilitado" => $record->habilitado,
                'conteo'=> $record->conteo,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return $getallconteo;
    }


    public function CerrarAll($idCarga) // este solo se va a ejecutar en la api desig
    {

        $allAsigCarga = DB::table('tab_asignacions')->where('id_carga', $idCarga)
            ->get();

        foreach ($allAsigCarga as $record) {
            DB::table('tab_asignacions')->where('id_carga', $idCarga)->update([
                "id_estatus" => 4,
            ]);
        }
        return $allAsigCarga;
    }
}
