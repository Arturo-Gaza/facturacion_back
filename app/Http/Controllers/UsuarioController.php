<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Classes\ApiResponseHelper;
use App\Http\Requests\UpdateUsuarioRequest;
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

    public function getCompras()
    {
        $usuario = $this->usuario->getCompras();
        return ApiResponseHelper::sendResponse($usuario, 'Sucess', 201);
    }

    public function getAllUser()
    {
        try {
            $getAllUser = $this->usuario->getAllUser();
            return ApiResponseHelper::sendResponse($getAllUser, 'Usuarios obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }


    public function getAllUserAlmacen($idCarga)
    {
        try {
            $getAllUserAlmacen = $this->usuario->getAllUserAlmacen($idCarga);
            return ApiResponseHelper::sendResponse($getAllUserAlmacen, 'Usuarios obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getAllUserAsignado($idCarga)
    {
        try {
            $getAllUserAsignado = $this->usuario->getAllUserAsignado($idCarga);
            return ApiResponseHelper::sendResponse($getAllUserAsignado, 'Usuarios obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener la lista', 500);
        }
    }

    public function getById($id)
    {
        try {
            $getById = $this->usuario->getByID($id);
            return ApiResponseHelper::sendResponse($getById, 'Usuario obtenido', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'No se pudo obtener el registro', 500);
        }
    }



    public function getAllHabilitados()
    {
        $usuario = $this->usuario->getAllHabilitados();
        return ApiResponseHelper::sendResponse($usuario, 'Sucess', 201);
    }

    public function enviarCorreoRec(Request $request)
    {
        $data = [
            'email' => $request->email,
        ];
        $usuario = $this->usuario->enviarCorreoRec($data);

        return ApiResponseHelper::sendResponse($usuario, 'Si el correo está registrado, se ha enviado un código de recuperación', 201);
    }
        public function enviarCorreoConf(Request $request)
    {
        $data = [
            'email' => $request->email,
        ];
        $usuario = $this->usuario->enviarCorreoConf($data);

        return ApiResponseHelper::sendResponse($usuario, 'Se ha enviado un código de confirmación', 201);
    }
        public function enviarCorreoInhabilitar(Request $request)
    {
        $data = [
            'email' => $request->email,
        ];
        $usuario = $this->usuario->enviarCorreoInhabilitar($data);

        return ApiResponseHelper::sendResponse($usuario, 'Se ha enviado un código de confirmación', 201);
    }
        public function enviarSMSConf(Request $request)
    {
        $data = [
            'phone' => $request->phone,
        ];
        $usuario = $this->usuario->enviarSMSConf($data);

        return ApiResponseHelper::sendResponse($usuario, 'Se ha enviado un código de confirmación', 201);
    }

    public function validarCorreoRec(Request $request)
    {
        $data = [
            'codigo' => $request->codigo,
            'email' => $request->email,
        ];
        $usuario = $this->usuario->validarCorreoRec($data);
        if (!$usuario) {
            return ApiResponseHelper::sendResponse($usuario, 'Código inválido', 400);
        }
        return ApiResponseHelper::sendResponse($usuario, 'Sucess', 201);
    }
        public function validarCorreoConf(Request $request)
    {
        $data = [
            'codigo' => $request->codigo,
            'email' => $request->email,
        ];
        $usuario = $this->usuario->validarCorreoConf($data);
        if (!$usuario) {
            return ApiResponseHelper::sendResponse($usuario, 'Código inválido', 400);
        }
        return ApiResponseHelper::sendResponse($usuario, 'Código Validado', 201);
    }

    public function validarCorreoInhabilitar(Request $request)
    {
        $data = [
            'codigo' => $request->codigo,
            'email' => $request->email,
        ];
        $usuario = $this->usuario->validarCorreoInhabilitar($data);
        if (!$usuario) {
            return ApiResponseHelper::sendResponse($usuario, 'Código inválido', 400);
        }
        return ApiResponseHelper::sendResponse($usuario, 'Código Validado', 201);
    }
    public function recPass(Request $request)
    {
        $data = [
            'codigo' => $request->codigo,
            'email' => $request->email,
            'nuevaPass' => $request->nuevaPass,
        ];

        try {
            $getById = $this->usuario->recPass($data);
            return ApiResponseHelper::sendResponse($getById, 'Contraseña cambiada con exito ', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'Ocurrio un error inesperado ', 500);
        }
    }

        public function desHabilitar(Request $request)
    {
        $data = [
            'codigo' => $request->codigo,
            'email' => $request->email
        ];

        try {
            $getById = $this->usuario->desHabilitar($data);
            return ApiResponseHelper::sendResponse($getById, 'Usuario bloqueado con exito ', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, 'Ocurrio un error inesperado ', 500);
        }
    }


    public function update(UpdateUsuarioRequest $request, string $id)
    {

        if ($request->password == null) {
            $data = [
                'name' => $request->name,
                'apellidoP' => $request->apellidoP,
                'apellidoM' => $request->apellidoM,
                'email' => $request->email,
                'user' => $request->user,
                'habilitado' => $request->habilitado,
                'idRol' => $request->idRol,
                'id_departamento'=> $request->id_departamento
            ];
        } else {
            $data = [
                'name' => $request->name,
                'apellidoP' => $request->apellidoP,
                'apellidoM' => $request->apellidoM,
                'email' => $request->email,
                'password' => $request->password,
                'user' => $request->user,
                'habilitado' => $request->habilitado,
                'idRol' => $request->idRol,
                'id_departamento'=> $request->id_departamento
            ];
        }
        DB::beginTransaction();
        try {
            if ($request->password == null) {
                $this->usuario->update($data, $id);
            } else {
                $this->usuario->updatePassword($data, $id);
            }
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Usuario actualizado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }

    public function deleteUser(Request $request, string $id)
    {
        $data = [
            'habilitado' => $request->habilitado,
            'user' => $request->user,
        ];

        DB::beginTransaction();
        try {
            $this->usuario->deleteUser($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Usuario eliminado correctamente', 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
    }
}
