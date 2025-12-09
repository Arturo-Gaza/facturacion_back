<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\ReversionRepositoryInterface;
use App\Models\ReversionSolicitud;
use App\Models\ReversionToken;
use App\Models\Solicitud;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReversionRepository implements ReversionRepositoryInterface
{
    public function crearSolicitud(int $id_solicitud, int $id_empleado): ReversionSolicitud
    {
        $now = Carbon::now();
        $soliciud=Solicitud::find($id_solicitud);
        $soliciud->estado_id=12;
        $soliciud->save();
        $rev = ReversionSolicitud::create([
            'id_emplado' => $id_empleado,
            'id_solicitud' => $id_solicitud,
            'estado' => 'PENDIENTE',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->auditar($rev->id, 'SOLICITUD_CREADA', ['id_solicitud' => $id_solicitud], $id_empleado);

        return $rev;
    }

    public function generarToken(int $id_solicitud, int $adminId, int $ttlMinutes = 5): string
    {
        
         $rev=ReversionSolicitud::where('id_solicitud',$id_solicitud)->first();
        if (!$rev) {
            throw new Exception('Solicitud no encontrada');
        }
        if ($rev->estado !== 'PENDIENTE') {
            throw new Exception('La solicitud no está en estado PENDIENTE');
        }

        $tokenPlain = str_pad((string) mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $secret = config('app.key');
        $tokenHash = hash_hmac('sha256', $tokenPlain, $secret);

        $now = Carbon::now();

        $token = ReversionToken::create([
            'reversion_id' => $rev->id,
            'token' => $tokenHash,
            'created_at' => $now,
            'created_por_admin' => $adminId,
            'used' => false,
            'used_at' => null,
        ]);

        $rev->estado = 'TOKEN_GENERADO';
        $rev->updated_at = $now;
        $rev->save();

        $this->auditar($id_solicitud, 'TOKEN_GENERADO', ['admin' => $adminId], $adminId);

        return $tokenPlain;
    }

    public function validarYUsarToken(int $id_solicitud, string $tokenPlain, int $operadorId): array
    {
       $rev=ReversionSolicitud::where('id_solicitud',$id_solicitud)->first();
       $reversionId=$rev->id;
        if (!$rev) {
            return ['exito' => false, 'detalle' => 'Solicitud no encontrada'];
        }
        if ($rev->estado !== 'TOKEN_GENERADO') {
            return ['exito' => false, 'detalle' => 'No hay token generado para esta solicitud'];
        }

        $tokenRecord = ReversionToken::where('reversion_id', $reversionId)
            ->where('used', false)
            ->orderByDesc('created_at')
            ->first();

        if (!$tokenRecord) {
            return ['exito' => false, 'detalle' => 'No existe token válido'];
        }

        $created = Carbon::parse($tokenRecord->created_at);
        if ($created->addMinutes(5)->lt(Carbon::now())) {
            return ['exito' => false, 'detalle' => 'Token expirado'];
        }

        $secret = config('app.key');
        $candidateHash = hash_hmac('sha256', $tokenPlain, $secret);

        if (!hash_equals($candidateHash, $tokenRecord->token)) {
            return ['exito' => false, 'detalle' => 'Token inválido'];
        }

        DB::beginTransaction();
        try {
            $tokenRecord->used = true;
            $tokenRecord->used_at = Carbon::now();
            $tokenRecord->save();

            $resultado = $this->ejecutarReversion($rev->id_solicitud, $operadorId);

            $rev->estado = $resultado['exito'] ? 'EJECUTADO' : 'RECHAZADO';
            $rev->updated_at = Carbon::now();
            $rev->save();

            $this->auditar($reversionId, 'EJECUTADO_CON_TOKEN', ['operador' => $operadorId, 'resultado' => $resultado], $operadorId);

            DB::commit();

            return ['exito' => $resultado['exito'], 'detalle' => $resultado];
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function rechazar(int $reversionId, int $adminId, ?string $motivo = null): ReversionSolicitud
    {
        $rev = $this->getById($reversionId);
        if (!$rev) {
            throw new Exception('Solicitud no encontrada');
        }
        if ($rev->estado === 'EJECUTADO') {
            throw new Exception('No se puede rechazar una solicitud ya ejecutada');
        }

        $rev->estado = 'RECHAZADO';
        $rev->updated_at = Carbon::now();
        $rev->save();

        $this->auditar($reversionId, 'RECHAZADA', ['admin' => $adminId, 'motivo' => $motivo], $adminId);

        return $rev;
    }

    // Métodos auxiliares que no forman parte de la interfaz pero son necesarios para el funcionamiento interno
    
    private function getById(int $id): ?ReversionSolicitud
    {
        return ReversionSolicitud::find($id);
    }

    private function auditar(int $reversionId, string $accion, ?array $detalles = null, ?int $usuarioId = null): void
    {
        try {
            Log::info('AUDITORIA_REV', [
                'reversion_id' => $reversionId,
                'accion' => $accion,
                'detalles' => $detalles,
                'usuario' => $usuarioId,
                'ts' => Carbon::now()->toDateTimeString(),
            ]);
        } catch (Exception $ex) {
            // noop
        }
    }

    private function ejecutarReversion(int $id_solicitud, int $operadorId): array
    {
        try {
            // Aquí debes llamar a la lógica real que invierte la solicitud.
            // Reemplaza por la llamada a tu servicio real.
            return ['exito' => true, 'mensaje' => 'Reversión ejecutada (stub)'];
        } catch (Exception $ex) {
            return ['exito' => false, 'mensaje' => $ex->getMessage()];
        }
    }
}