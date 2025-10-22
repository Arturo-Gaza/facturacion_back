<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\SolicitudRepositoryInterface;
use App\Interfaces\SistemaFacturacion\SuscripcionesRepositoryInterface;
use App\Models\CatPlanes;
use App\Models\DatosFiscal;
use App\Models\SistemaTickets\CatEstatusSolicitud;
use App\Models\SistemaTickets\TabBitacoraSolicitud;
use App\Models\Solicitud;
use App\Models\Suscripciones;
use App\Services\AIDataExtractionService;
use App\Services\OCRService;
use Exception;
use Illuminate\Support\Carbon;

class SuscripcionesRepository implements SuscripcionesRepositoryInterface
{


    public function getAll()
    {
        return Suscripciones::with(['usuario', 'empleado', 'estadoSolicitud'])->get();
    }
    public function iniciar($id_user, $id_plan)
    {


        $plan = CatPlanes::findOrFail($id_plan);
        $exist = Suscripciones::where('usuario_id', $id_user)
            ->where('id_plan', $plan->id)
            ->where('estado', Suscripciones::ESTADO_ACTIVA)
            ->first();
        if ($exist) {
            throw new Exception("Ya existe una suscripción activa para este plan");
        }
        $precio = $plan->precio ?? 0;
        if ($precio == 0) {
            $vigencia_fin = $plan->esMensual() ? Carbon::now()->addMonth() : null;

            $sus = Suscripciones::create([
                'usuario_id' => $id_user,
                'id_plan' => $plan->id,
                'fecha_inicio' => now(),
                'fecha_vencimiento' => $vigencia_fin, // ejemplo: 1 mes
                'estado' => Suscripciones::ESTADO_ACTIVA,
                'perfiles_utilizados' => 0,
                'facturas_realizadas' => 0,
            ]);

            return $sus;
        }
    }
}
