<?php

namespace App\Interfaces\Usuario;

use App\Models\User;

interface UsuarioRepositoryInterface
{
    public function getAll();
    public function getAllUser();
    public function getAllUserAlmacen($idCarga);
    public function getAllUserAsignado($idCarga);
    public function getByID($id);
    public function getAllHabilitados();
    public function findByEmailOrUser(string $email): ?User;
    public function responseUser(string $email);
    public function aumentarIntento(int $intentos, $id);
    public function store(array $data);
    public function update(array $data, $id);
    public function updatePassword(array $data, $id);
    public function generateToken(User $user): string;
    public function loginActive(int $id);
    public function loginInactive(int $id);
}
