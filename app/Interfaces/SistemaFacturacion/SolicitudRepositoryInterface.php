<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\Solicitud;
use Illuminate\Http\Request;

interface SolicitudRepositoryInterface
{
  public function getAll();
  public function calcularPrecio($id_solicitud,$id_user);
  public function getFacturaPDF($id);
  public function getFacturaXML($id);
  public function getConsola($idUsr);
  public function subirFactura($idUsr, $pdf, $xml, $id_solicitud);
  public function getMesaAyuda();
  public function getDashboard($fecha_inicio,$fecha_fin,$id);
    public function concluir($id_usuario,$id_solicitud);
  public function getByUsuario(int $usuario_id);
  public function procesar(int $id);
  public function rechazar($id_solicitud, $id_motivo_rechazo,$id_user);
  public function actualizarEstatus($id_solicitud, $id_estatus, $id_usuario);
  public function enviar(int $id, int $id_user);
  public function asignar($id_user, $id_solicitud, $id_empleado);
  public function actualizarReceptor(Request $data);
  public function eliminar(int $id);
  public function obtenerImagen(int $id);
  public function getGeneralByUsuario($fecha_inicio ,$fecha_fin,$usuario_id);
  public function getByID($id): ?Solicitud;
  public function getTodosDatos($id);
  public function store(Request $data, $id_user): Solicitud;
  public function update(array $data, $id): ?Solicitud;
  public function editarTicket(array $data, $id): ?Solicitud;
}
