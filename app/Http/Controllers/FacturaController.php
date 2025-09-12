<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Interfaces\SistemaFacturacion\FacturaRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacturaController extends Controller
{
    protected $facturaRepository;

    public function __construct(FacturaRepositoryInterface $facturaRepository)
    {
        $this->facturaRepository = $facturaRepository;
    }

    public function getAll()
    {
        try {
            $all = $this->facturaRepository->getAll();
            return ApiResponseHelper::sendResponse($all, 'Facturas obtenidas', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $factura = $this->facturaRepository->getByID($id);
            return ApiResponseHelper::sendResponse($factura, 'Factura obtenida', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'solicitud_id',
                'empleado_id',
                'servicio_id',
                'monto',
                'archivo_factura'
            ]);
            $factura = $this->facturaRepository->store($data);

            DB::commit();
            return ApiResponseHelper::sendResponse($factura, 'Factura creada correctamente', 201);
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
                'solicitud_id',
                'empleado_id',
                'servicio_id',
                'monto',
                'archivo_factura'
            ]);
            $updated = $this->facturaRepository->update($data, $id);

            DB::commit();
            return ApiResponseHelper::sendResponse($updated, 'Factura actualizada correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}