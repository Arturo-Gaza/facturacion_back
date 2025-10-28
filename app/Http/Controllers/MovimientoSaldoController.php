<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Interfaces\SistemaFacturacion\MovimientoSaldoRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimientoSaldoController extends Controller
{
    protected $movimientoSaldoRepository;

    public function __construct(MovimientoSaldoRepositoryInterface $movimientoSaldoRepository)
    {
        $this->movimientoSaldoRepository = $movimientoSaldoRepository;
    }

    public function getAll()
    {
        try {
            $all = $this->movimientoSaldoRepository->getAll();
            return ApiResponseHelper::sendResponse($all, 'Movimientos de saldo obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $movimiento = $this->movimientoSaldoRepository->getByID($id);
            return ApiResponseHelper::sendResponse($movimiento, 'Movimiento de saldo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

        public function getMyMovimientos()
    {
        try {
            $idUsr = auth()->user()->id;
            $movimiento = $this->movimientoSaldoRepository->getMyMovimientos($idUsr);
            return ApiResponseHelper::sendResponse($movimiento, 'Movimiento de saldo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'usuario_id',
                'monto',
                'tipo_movimiento_id',
                'nuevo_monto',
                'factura_id',
                'descripcion'
            ]);
            $movimiento = $this->movimientoSaldoRepository->store($data);

            DB::commit();
            return ApiResponseHelper::sendResponse($movimiento, 'Movimiento de saldo creado correctamente', 201);
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
                'usuario_id',
                'monto',
                'tipo_movimiento_id',
                'nuevo_monto',
                'factura_id',
                'descripcion'
            ]);
            $updated = $this->movimientoSaldoRepository->update($data, $id);

            DB::commit();
            return ApiResponseHelper::sendResponse($updated, 'Movimiento de saldo actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}