<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Interfaces\SistemaFacturacion\PrecioRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrecioController extends Controller
{
    protected $precioRepository;

    public function __construct(PrecioRepositoryInterface $precioRepository)
    {
        $this->precioRepository = $precioRepository;
    }

    public function getAll()
    {
        try {
            $all = $this->precioRepository->getAll();
            return ApiResponseHelper::sendResponse($all, 'Precios obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $precio = $this->precioRepository->getByID($id);
            return ApiResponseHelper::sendResponse($precio, 'Precio obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'servicio_id',
                'precio',
                'vigencia_desde',
                'vigencia_hasta'
            ]);
            $precio = $this->precioRepository->store($data);

            DB::commit();
            return ApiResponseHelper::sendResponse($precio, 'Precio creado correctamente', 201);
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
                'servicio_id',
                'precio',
                'vigencia_desde',
                'vigencia_hasta'
            ]);
            $updated = $this->precioRepository->update($data, $id);

            DB::commit();
            return ApiResponseHelper::sendResponse($updated, 'Precio actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}