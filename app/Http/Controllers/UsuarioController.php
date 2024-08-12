<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Classes\ApiResponseHelper;
use App\Interfaces\Usuario\UsuarioRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    protected $usuario;

    public function __construct(UsuarioRepositoryInterface $usuario)
    {
        $this->usuario = $usuario;
    }
    public function getAll()
    {
        $usuario = $this->usuario->getAll();
        return ApiResponseHelper::sendResponse($usuario, 'Sucess', 201);
    }

    public function getById($id)
    {
        try {
            $getById = $this->usuario->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Usuario obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudo obtener el registro', 500);
        }
    }

    public function getAllHabilitados()
    {
        $usuario = $this->usuario->getAllHabilitados();
        return ApiResponseHelper::sendResponse($usuario, 'Sucess', 201);
    }

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $this->usuario->update($request->all(), $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Usuario actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
