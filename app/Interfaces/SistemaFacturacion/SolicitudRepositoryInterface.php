<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\Solicitud;
use Illuminate\Http\Request;
interface SolicitudRepositoryInterface
{
    public function getAll();
     public function getByUsuario(int $usuario_id);
     public function procesar(int $id);
      public function enviar(int $id);
     public function obtenerImagen(int $id);
     public function getGeneralByUsuario(int $usuario_id);
    public function getByID($id): ?Solicitud;
    public function store(Request $data): Solicitud;
    public function update(array $data, $id): ?Solicitud;
}