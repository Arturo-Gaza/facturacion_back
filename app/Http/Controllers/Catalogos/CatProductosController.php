<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\ApiResponseHelper;
use App\Http\Requests\Catalogos\Store\StoreCatProductosRequest;
use App\Http\Requests\Catalogos\Store\StoreCatProductosSinClaveRequest;
use App\Http\Requests\Catalogos\Update\UpdateProductosRequest;
use App\Interfaces\Catalogos\CatProductosRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class CatProductosController extends Controller
{
    protected $_catProductos;

    public function __construct(CatProductosRepositoryInterface $catProductos)
    {
        $this->_catProductos = $catProductos;
    }

    public function getAll()
    {
        try {
            $getAll = $this->_catProductos->getAll();
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function search(Request $req)
    {
        try {
            $data = [
                'modo' => $req->modo,
                'categoria' => $req->categoria,
                'termino' => $req->termino,
            ];
            $getAll = $this->_catProductos->search($data);
            return ApiResponseHelper::sendResponse($getAll, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }


    public function getAllPersonalizado($idCarga)
    {
        try {
            $getAllPersonalizado = $this->_catProductos->getAllPersonalizado($idCarga);
            return ApiResponseHelper::sendResponse($getAllPersonalizado, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->_catProductos->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }
    public function getByCategoria($id)
    {
        try {
            $getById = $this->_catProductos->getBygetByCategoria($id);
            return ApiResponseHelper::sendResponse($getById, 'Catálogo obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function store(StoreCatProductosRequest $cat)
    {
        DB::beginTransaction(); // Inicia la transacción
        try {
            // Prepara los datos para la creación
            $data = [
                'id_unidad_medida' => $cat->id_unidad_medida,
                'id_gpo_familia' => $cat->id_gpo_familia,
                'id_moneda' => $cat->id_moneda,
                'clave_producto' => $cat->clave_producto,
                'descripcion_producto' => $cat->descripcion_producto,
                'habilitado' => $cat->habilitado,
                'id_categoria' => $cat->id_categoria,
            ];

            // Llama al método store en el repositorio para crear el registro
            $producto = $this->_catProductos->store($data);

            DB::commit(); // Confirma la transacción si todo está bien

            // Envía una respuesta exitosa
            return ApiResponseHelper::sendResponse($producto, 'Producto creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack(); // Revierte la transacción en caso de error
            // Envía una respuesta de error
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function storeSinClave(StoreCatProductosSinClaveRequest $cat)
    {
        DB::beginTransaction(); // Inicia la transacción
        try {
            // Prepara los datos para la creación
            $data = [
                'id_unidad_medida' => $cat->id_unidad_medida,
                'id_gpo_familia' => $cat->id_gpo_familia,
                'id_moneda' => $cat->id_moneda,
                'clave_producto' => $cat->clave_producto,
                'descripcion_producto' => $cat->descripcion_producto,
                'habilitado' => $cat->habilitado,
                'id_categoria' => $cat->id_categoria,
            ];

            // Llama al método store en el repositorio para crear el registro
            $producto = $this->_catProductos->store($data);

            DB::commit(); // Confirma la transacción si todo está bien

            // Envía una respuesta exitosa
            return ApiResponseHelper::sendResponse($producto, 'Producto creado correctamente', 201);
        } catch (Exception $ex) {
            DB::rollBack(); // Revierte la transacción en caso de error
            // Envía una respuesta de error
            return ApiResponseHelper::rollback($ex);
        }
    }


    public function update(UpdateProductosRequest $cat, $id)
    {
        DB::beginTransaction();
        try {
            $data = [
                'id_unidad_medida' => $cat->id_unidad_medida,
                'id_gpo_familia' => $cat->id_gpo_familia,
                'id_moneda' => $cat->id_moneda,
                'clave_producto' => $cat->clave_producto,
                'descripcion_producto' => $cat->descripcion_producto,
                'habilitado' => $cat->habilitado,
                'id_categoria' => $cat->id_categoria,
            ];
            $this->_catProductos->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Producto actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
    public function exportar(Request $data)
    {
        DB::beginTransaction();
        try {
            $filtro = trim($data->getContent(), '"');
            $archivo = $this->_catProductos->exportar($filtro);
            DB::commit();
            return ApiResponseHelper::sendResponse($archivo, 'Articulos exportados correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
