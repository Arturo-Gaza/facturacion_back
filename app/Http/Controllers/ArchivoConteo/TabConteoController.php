<?php

namespace App\Http\Controllers\ArchivoConteo;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArchivoConteo\Store\StoreTabConteoRequest;
use App\Interfaces\ArchivoConteo\TabConteoRepositoryInterface;
use Illuminate\Http\Request;
use App\Classes\ApiResponseHelper;
use App\Http\Requests\ArchivoConteo\Update\UpdateTabConteoRequest;
use Exception;
use Illuminate\Support\Facades\DB;

class TabConteoController extends Controller
{
    protected $_TabConteo;

    public function __construct(TabConteoRepositoryInterface $_TabConteo)
    {
        $this->_TabConteo = $_TabConteo;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_TabConteo->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getByIDCarga($idCarga)
    {
        try {
            $getByIDCarga = $this->_TabConteo->getByIDCarga($idCarga);
            return ApiResponseHelper::sendResponse($getByIDCarga, 'Conteo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getByIDCargaIDUser($idCarga, $idUser)
    {
        try {
            $getByIDCargaIDUser = $this->_TabConteo->getByIDCargaIDUser($idCarga, $idUser);
            return ApiResponseHelper::sendResponse($getByIDCargaIDUser, 'Conteo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_TabConteo->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreTabConteoRequest $cat)
    {
        // $exists = DB::table('tab_conteo')
        //         ->where('id_carga', $cat->id_carga)
        //         ->orWhere('id_usuario', $cat->id_usuario)
        //         ->orWhere('id_producto', $cat->id_producto)
        //         ->orWhere('ubicacion', $cat->ubicacion)
        //         ->exists();

        $exists = DB::table('tab_conteo')
            ->where('id_carga', $cat->id_carga)
            ->where('id_usuario', $cat->id_usuario)
            ->where('conteo', $cat->conteo)
            ->where('id_producto', $cat->id_producto)
            ->where('ubicacion', $cat->ubicacion)
            ->get();

        if ($exists->count() != 0) {
            $errors = ['Ya se cuenta con este registro.'];
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error',
                'errors' => $errors,
                'data' => $exists,
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = [
                'id_carga' => $cat->id_carga,
                'id_usuario' => $cat->id_usuario,
                'id_almacen' => $cat->id_almacen,
                'id_unidadmedida' => $cat->id_unidadmedida,
                'id_grupofamilia' => $cat->id_grupofamilia,
                'id_producto' => $cat->id_producto,
                'id_ubicacion' => $cat->id_ubicacion,
                'codigo' => $cat->codigo,
                'descripcion' => $cat->descripcion,
                'ume' => $cat->ume,
                'cantidad' => $cat->cantidad,
                'ubicacion' => $cat->ubicacion,
                'observaciones' => $cat->observaciones,
                'habilitado' => $cat->habilitado,
                'conteo' => $cat->conteo,
            ];
            $_TabConteo = $this->_TabConteo->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(true, 'Conteo creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(UpdateTabConteoRequest $cat, $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_carga' => $cat->id_carga,
                'id_usuario' => $cat->id_usuario,
                'id_almacen' => $cat->id_almacen,
                'id_unidadmedida' => $cat->id_unidadmedida,
                'id_grupofamilia' => $cat->id_grupofamilia,
                'id_producto' => $cat->id_producto,
                'id_ubicacion' => $cat->id_ubicacion,
                'codigo' => $cat->codigo,
                'descripcion' => $cat->descripcion,
                'ume' => $cat->ume,
                'cantidad' => $cat->cantidad,
                'ubicacion' => $cat->ubicacion,
                'observaciones' => $cat->observaciones,
                'habilitado' => $cat->habilitado,
                'conteo' => $cat->conteo,
            ];

            $this->_TabConteo->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Conteo actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }


    public function DeleteAll($idCarga, $idUsuario, $numConteo)
    {
        $allconteo = DB::table('tab_conteo')
            ->where('id_carga', $idCarga)
            ->where('id_usuario', $idUsuario)
            ->where('conteo', $numConteo)
            ->get();

        foreach ($allconteo as $record) {
            DB::table('tab_conteo')
                ->where('id_carga', $idCarga)
                ->where('id_usuario', $idUsuario)
                ->where('conteo', $numConteo)
                ->delete();
        }

        //return $allconteo;
        return ApiResponseHelper::sendResponse($allconteo, 'Se eliminaron correctamente los registros', 201);
    }

    public function getConteosGeneral($idCarga, $conteo)
    {
        try {
            $getConteos = $this->_TabConteo->reporteGeneral($idCarga, $conteo);
            return ApiResponseHelper::sendResponse($getConteos, 'Conteo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getDiferenciasConteo($idCarga, $conteo)
    {
        try {
            $getConteos = $this->_TabConteo->reporteDiferencias($idCarga, $conteo);
            return ApiResponseHelper::sendResponse($getConteos, 'Conteo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getNoConteosByCarga($idCarga)
    {
        try {
            $getConteos = $this->_TabConteo->getConteoByIdCarga($idCarga);
            return ApiResponseHelper::sendResponse($getConteos, 'Lista de conteos obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getConteoConcentrado($idCarga,$numConteo)
    {
        try {
            $getConteos = $this->_TabConteo->reporteConcentrado($idCarga,$numConteo);
            return ApiResponseHelper::sendResponse($getConteos, 'Lista del condentrado de conteos obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener la lista', 500);
        }
    }
}
