<?php

namespace App\Repositories\Usuario;

use App\Interfaces\Usuario\UsuarioRepositoryInterface;
use App\Models\User;
use App\Models\UserSistema;
use App\Models\UsuarioRol;
use Illuminate\Support\Facades\Hash;

class UsuarioRepository implements UsuarioRepositoryInterface
{
    public function getAll()
    {
        $usuario = User::select('users.id', 'users.user', 'users.name', 'users.apellidoP', 'users.apellidoM', 'users.email', 'users.idRol','users.habilitado', 'cat_roles.nombre')
            ->join('cat_roles', 'cat_roles.id', '=', 'users.idRol')->get();
        return $usuario;

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
