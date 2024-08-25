<?php

namespace App\Repositories\Usuario;

use App\Interfaces\Usuario\UsuarioRepositoryInterface;
use App\Models\AsignacionCarga\tab_asignacion;
use App\Models\User;
use App\Models\UserSistema;
use App\Models\UsuarioRol;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class UsuarioRepository implements UsuarioRepositoryInterface
{
    public function getAll()
    {
        $usuario = User::select('users.id', 'users.user', 'users.name', 'users.apellidoP', 'users.apellidoM', 'users.email', 'users.idRol', 'users.habilitado', 'cat_roles.nombre')
            ->join('cat_roles', 'cat_roles.id', '=', 'users.idRol')->get();
        return $usuario;
    }

    public function getAllUserAlmacen($idCarga)
    {
        $usuario = User::select(
            'users.id',
            'users.user',
            'users.name',
            'users.apellidoP',
            'users.apellidoM',
            'users.email',
            'users.idRol',
            'users.habilitado',
        )
            ->leftJoin('tab_asignacions', 'tab_asignacions.id_usuario', '=', 'users.id')
            ->where(function ($query) use ($idCarga) {
                $query->where('tab_asignacions.id_usuario', null)
                    ->orWhere('tab_asignacions.habilitado', 0)
                    ->orWhere('tab_asignacions.id_carga', '!=', $idCarga);
            })
            ->groupBy('users.id')
            ->get()->filter(function ($user) {
                return $user->idRol == 2;
            });

        $results = array();
        foreach ($usuario as $val) {
            $results[] = $val;
        }

        return $results;
    }

    public function getAllUserAsignado($idCarga)
    {
        $usuario = User::select(
            'users.id',
            'users.user',
            'users.name',
            'users.apellidoP',
            'users.apellidoM',
            'users.email',
            'users.idRol',
            'users.habilitado',
            'tab_asignacions.habilitado AS asigHabilitado',
        )
            ->join('tab_asignacions', 'tab_asignacions.id_usuario', '=', 'users.id')
            ->orWhere('tab_asignacions.id_carga', '=', $idCarga)
            ->groupBy('users.id')
            ->groupBy('tab_asignacions.habilitado')
            ->get()->filter(function ($user) {
                return $user->asigHabilitado == 1 && $user->idRol == 2;
            });

        $results = array();
        foreach ($usuario as $val) {
            $results[] = $val;
        }

        return $results;
    }


    public function getByID($id): ?User
    {
        return User::where('id', $id)->first();
    }

    public function getAllHabilitados()
    {
        return User::where('habilitado', 1)->get();
    }

    public function findByEmailOrUser(string $email): ?User
    {
        return User::where('email', $email)->orWhere('user', $email)->first();
    }

    public function responseUser(string $email)
    {
        $usuario = User::select('users.id', 'users.user', 'users.name', 'users.idRol', 'users.email', 'users.apellidoP', 'users.apellidoM', 'cat_roles.nombre')
            ->join('cat_roles', 'cat_roles.id', '=', 'users.idRol')->where('users.email', $email)->orWhere('users.user', $email)->first();
        return $usuario;
    }

    public function store(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function update(array $data, $id)
    {
        $data['password'] = Hash::make($data['password']);
        return User::whereId($id)->update($data);
    }

    public function aumentarIntento(int $intentos, $id)
    {
        User::where('id', $id)->update(array('intentos' => $intentos + 1));
    }

    public function generateToken(User $user): string
    {
        return $user->createToken('API Token')->plainTextToken;
    }

    public function loginActive(int $id)
    {
        User::where('id', $id)->update(array('login_activo' => true));
    }

    public function loginInactive(int $id)
    {
        User::where('id', $id)->update(array('login_activo' => false));
    }
}
