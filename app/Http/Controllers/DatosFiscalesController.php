<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Interfaces\DatosFiscalesRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatosFiscalesController extends Controller
{
    protected $datosFiscalesRepository;

    public function __construct(DatosFiscalesRepositoryInterface $datosFiscalesRepository)
    {
        $this->datosFiscalesRepository = $datosFiscalesRepository;
    }

    public function getAll()
    {
        try {
            $all = $this->datosFiscalesRepository->getAll();
            return ApiResponseHelper::sendResponse($all, 'Datos fiscales obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista de datos fiscales', 500);
        }
    }

    public function extraerDatosCFDI(Request $data)
    {
        try {
            $all = $this->datosFiscalesRepository->extraerDatosCFDI($data);
            return ApiResponseHelper::sendResponse($all, 'Datos fiscales obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista de datos fiscales', 500);
        }
    }

    public function getById($id)
    {
        try {
            $datosFiscales = $this->datosFiscalesRepository->getByID($id);
            if (!$datosFiscales) {
                return ApiResponseHelper::sendResponse(null, 'Registro no encontrado', 404);
            }
            return ApiResponseHelper::sendResponse($datosFiscales, 'Datos fiscales obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }
    public function getByUsr($id)
    {
        try {
            $datosFiscales = $this->datosFiscalesRepository->getByUsr($id);
            if (!$datosFiscales) {
                return ApiResponseHelper::sendResponse(null, 'Registros no encontrados', 404);
            }
            return ApiResponseHelper::sendResponse($datosFiscales, 'Datos fiscales obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'id_usuario',
                'nombre_razon',
                'primer_apellido',
                'segundo_apellido',
                'nombre_comercial',
                'es_persona_moral',
                'rfc',
                'curp',
                'id_regimen',
                'fecha_inicio_op',
                'id_estatus_sat',
                'datos_extra',
                'email_facturacion_id'
            ]);

            $datosFiscales = $this->datosFiscalesRepository->store($data);

            DB::commit();
            return ApiResponseHelper::sendResponse($datosFiscales, 'Datos fiscales creados correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
    public function storeConDomicilio(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'id_usuario',
                'nombre_razon',
                'primer_apellido',
                'segundo_apellido',
                'nombre_comercial',
                'rfc',
                'curp'

            ]);
            $direccionData = $request->input('direccion');
            $datosFiscales = $this->datosFiscalesRepository->storeConDomicilio($data, $direccionData);

            DB::commit();
            return ApiResponseHelper::sendResponse($datosFiscales, 'Datos fiscales creados correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function storeCompleto(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'id_usuario',
                'nombre_razon',
                'primer_apellido',
                'segundo_apellido',
                'es_persona_moral',
                'rfc',
                'curp',
                'id_estatus_sat',
                'idCIF',
                'lugar_emision',
                'fecha_emision',
                'fecha_ult_cambio_op',
                'fecha_inicio_op',
                'predeterminado'
            ]);
            $direccionData = $request->input('domicilioFiscal');
            $regimenesData = $request->input('regimenesFiscales');
            $datosFiscales = $this->datosFiscalesRepository->storeCompleto($data, $direccionData, $regimenesData);

            DB::commit();
            return ApiResponseHelper::sendResponse($datosFiscales, 'Datos fiscales creados correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function updateCompleto(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'id',
                'idDatosFiscales',
                'id_usuario',
                'nombre_razon',
                'primer_apellido',
                'segundo_apellido',
                'nombre_comercial',
                'es_persona_moral',
                'rfc',
                'curp',
                'id_estatus_sat',
                'idCIF',
                'lugar_emision',
                'fecha_emision',
                'fecha_ult_cambio_op',
                'fecha_inicio_op',
                'predeterminado'
            ]);
            $direccionData = $request->input('domicilioFiscal');
            $regimenesData = $request->input('regimenesFiscales');
            $idDatosFiscales=$data["id"];
            $datosFiscales = $this->datosFiscalesRepository->updateCompleto( $data,  $direccionData,  $regimenesData, $idDatosFiscales);

            DB::commit();
            return ApiResponseHelper::sendResponse($datosFiscales, 'Datos fiscales creados correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'id_usuario',
                'nombre_razon',
                'primer_apellido',
                'segundo_apellido',
                'nombre_comercial',
                'es_persona_moral',
                'rfc',
                'curp',
                'id_regimen',
                'fecha_inicio_op',
                'id_estatus_sat',
                'datos_extra',
                'email_facturacion_id'
            ]);

            $updated = $this->datosFiscalesRepository->update($data, $id);

            if (!$updated) {
                return ApiResponseHelper::sendResponse(null, 'Registro no encontrado', 404);
            }

            DB::commit();
            return ApiResponseHelper::sendResponse($updated, 'Datos fiscales actualizados correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
