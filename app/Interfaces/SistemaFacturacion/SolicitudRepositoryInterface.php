<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\Solicitud;
use Illuminate\Http\Request;
interface SolicitudRepositoryInterface
{
    public function getAll();
    public function getConsola($idUsr);
     public function getMesaAyuda();
     public function getByUsuario(int $usuario_id);
     public function procesar(int $id);
      public function enviar(int $id, int $id_user);
      public function asignar($id_user,$id_solicitud,$id_empleado);
      public function actualizarReceptor(Request $data);
      public function eliminar(int $id);
     public function obtenerImagen(int $id);
     public function getGeneralByUsuario(int $usuario_id);
    public function getByID($id): ?Solicitud;
    public function store(Request $data, $id_user): Solicitud;
    public function update(array $data, $id): ?Solicitud;
    public function editarTicket(array $data, $id): ?Solicitud;
}