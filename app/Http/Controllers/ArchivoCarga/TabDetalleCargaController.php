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
}
