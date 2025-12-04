<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\ReversionSolicitud;
use Illuminate\Http\Request;

interface ReversionRepositoryInterface
{
    public function crearSolicitud(int $id_solicitud, int $id_empleado): ReversionSolicitud;
    public function generarToken(int $reversionId, int $adminId, int $ttlMinutes = 5): string;
    public function validarYUsarToken(int $reversionId, string $tokenPlain, int $operadorId): array;
    public function rechazar(int $reversionId, int $adminId, ?string $motivo = null): ReversionSolicitud;
}