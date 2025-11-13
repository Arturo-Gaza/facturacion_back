<?php

namespace App\Interfaces\Usuario;

use App\Models\User;

interface UsuarioRepositoryInterface
{
  public function getAll();
  public function getMesaAyuda();
  public function getAllUser();
  public function getColaboradores($id);
  public function getAllUserAlmacen($idCarga);
  public function getAllUserAsignado($idCarga);
  public function getByID($id);
  public function getDatos($id);
  public function editarDatos($data, $id);
  public function getAllHabilitados();
  public function enviarCorreoRec($data);
  public function enviarCorreoConf($data);
   public function enviarCorreoValReceptor($data);
    public function enviarCorreoCambiarCorreo($data);
  public function enviarSMSConf($data);
  public function validarCorreoRec($data);
  public function validarCorreoConf($data);
    public function validarCorreoValReceptor($data);
  public function enviarCorreoInhabilitar($data);
  public function validarCorreoInhabilitar($data);
  public function enviarCorreoEliminar($data);
  public function validarCorreoEliminar($data);
  public function recPass($data);
  public function desHabilitar($data);
  public function eliminar($data);
  public function desHabilitarPorAdmin($data);
  public function eliminarPorAdmin($data);
  public function findByEmailOrUser(string $email): ?User;
  public function responseUser(string $email);
  public function aumentarIntento(int $intentos, $id);
  public function store(array $data);
  public function storeCliente(array $data);
  public function storeHijo(array $data);
  public function completarHijo(array $data);
  public function update(array $data, $id);
  public function deleteUser(array $data, $id);
  public function updatePassword(array $data, $id);
  public function generateToken(User $user): string;
  public function loginActive(int $id);
  public function loginInactive(int $id);
}
