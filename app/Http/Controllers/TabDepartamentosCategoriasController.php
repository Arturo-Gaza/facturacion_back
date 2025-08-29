<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Classes\ApiResponseHelper;
use App\Http\Requests\SistemaTickets\Store\StoreSolicitudDetalleRequest;
use App\Interfaces\TabSolicitudesDetalleRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Interfaces\TabDepartamentosCategoriasRepositoryInterface;
use App\Models\Seguridad\Catalogos\TblRolMenu;
use App\Models\SistemaTickets\TabDepartamentosCategorias;
use Illuminate\Support\Facades\DB;

class TabDepartamentosCategoriasController extends Controller
{
    protected $tabDepartamentosCategoria;

    public function __construct(TabDepartamentosCategoriasRepositoryInterface $tabDepartamentosCategoria)
    {
        $this->tabDepartamentosCategoria = $tabDepartamentosCategoria;
    }

    public function getAll()
    {
        try {
            $getAll = $this->tabDepartamentosCategoria->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'TabDepartamentosCategoria obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex->getMessage(), 'No se pudo obtener la lista', 500);
        }
    }

    public function getByDep($id)
    {
        try {
            $getAll = $this->tabDepartamentosCategoria->getByDep($id);
            return ApiResponseHelper::sendResponse($getAll, 'TabDepartamentosCategoria obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex->getMessage(), 'No se pudo obtener la lista', 500);
        }
    }



    public function AddDelete(Request $request)
    {
        $agregarData = $request->input('Agregar', []);
        $eliminarData = $request->input('Eliminar', []);

        // Construimos objetos RolMenu para agregar
        $elementoaAgregar = collect($agregarData)->map(function ($item) {
            return [
                'id_departamento' => $item['id_departamento'],
                'id_categoria' => $item['id_categoria'],
            ];
        });

        // Construimos objetos RolMenu para eliminar (puedes usar como criterio para delete)
        $elementoaEliminar = collect($eliminarData)->map(function ($item) {
            return new TabDepartamentosCategorias([
                'id_departamento' => $item['id_departamento'],
                'id_categoria' => $item['id_categoria'],
            ]);
        });
        try {
            DB::transaction(function () use ($elementoaAgregar, $elementoaEliminar) {
                foreach ($elementoaEliminar as $elemento) {
                    $this->tabDepartamentosCategoria->delete(
                        $elemento->id_departamento,
                        $elemento->id_categoria
                    );
                }

                foreach ($elementoaAgregar as $rol) {
                    $this->tabDepartamentosCategoria->store($rol);
                }
            });
            return ApiResponseHelper::sendResponse(null, 'Categorias asignadas correctamente', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseHelper::rollback($e->getMessage(), 'Error al actualizar los datos', 500);
        } catch (\Exception $e) {
            return ApiResponseHelper::rollback($e->getMessage(), 'Error al actualizar los datos', 500);
        }
    }




    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_departamento' => $request->id_departamento,
                'id_categoria' => $request->id_categoria,
            ];

            $data = $this->tabDepartamentosCategoria->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse($data, 'Registro creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }


    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {


            $data = $this->tabDepartamentosCategoria->delete($request->id_departamento,$request->id_categoria);
            DB::commit();
            return ApiResponseHelper::sendResponse($data, 'Registro creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
