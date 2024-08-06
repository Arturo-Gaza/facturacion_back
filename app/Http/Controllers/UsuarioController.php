<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Classes\ApiResponseHelper;
use App\Interfaces\Usuario\UsuarioRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected $usuario;

    public function __construct(UsuarioRepositoryInterface $usuario)
    {
        $this->usuario = $usuario;
    }
    public function getAll()
    {
        //
        $usuario = $this->usuario->getAll();
        return ApiResponseHelper::sendResponse($usuario, 'Sucess',201);

    }
    public function getAllHabilitados()
    {
        //
        $usuario = $this->usuario->getAllHabilitados();
        return ApiResponseHelper::sendResponse($usuario, 'Sucess',201);

    }
    /**
     * Display the specified resource.
     */
    public function getPermisosByUsuario(string $id)
    {
        //
        try {
            $getById = $this->usuario->getPermisosByUsuario($id);
            return ApiResponseHelper::sendResponse($getById, 'Permisos obtenidos', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::sendResponse($ex, 'No se pudieron obtener los registros', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        DB::beginTransaction();
        try {
           $this->usuario->update($request->all(),$id);

          //  $this->usuario->update($data,$id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Usuario actualizado correctamente',200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */

}
