<?php

namespace App\Interfaces\Usuario;

use App\Models\User;

interface UsuarioRepositoryInterface
{
    public function getAll();
    public function getAllHabilitados();
    public function findByEmailOrUser(string $email): ?User;
    public function aumentarIntento(int $intentos, $id);
    public function store(array $data);
    public function update(array $data, $id);
    public function generateToken(User $user): string;
    public function getPermisosByUsuario(int $id);
}
