<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\CatPlanesRepositoryInterface;
use App\Models\CatPlanes;
use Illuminate\Support\Carbon;

class CatPlanesRepository implements CatPlanesRepositoryInterface
{
    public function getAll()
    {
        return CatPlanes::all();
    }
    public function getAllVigentes()
    {
        $now = Carbon::now();

        $planes = CatPlanes::where(function ($query) use ($now) {
            // Planes con vigencia_inicio <= ahora Y vigencia_fin >= ahora
            $query->where('vigencia_inicio', '<=', $now)
                ->where('vigencia_fin', '>=', $now);
        })->orWhere(function ($query) use ($now) {
            // O planes con vigencia_inicio nula (sin fecha de inicio específica) pero vigencia_fin >= ahora
            $query->whereNull('vigencia_inicio')
                ->where('vigencia_fin', '>=', $now);
        })->orWhere(function ($query) use ($now) {
            // O planes con vigencia_fin nula (sin fecha de fin) pero vigencia_inicio <= ahora
            $query->where('vigencia_inicio', '<=', $now)
                ->whereNull('vigencia_fin');
        })->orWhere(function ($query) {
            // O planes sin fechas de vigencia (siempre vigentes)
            $query->whereNull('vigencia_inicio')
                ->whereNull('vigencia_fin');
        })->with('preciosVigentes')->get();
        $planes->each(function ($plan) {
            $plan->operaciones_gratis = $this->getOperacionesGratis($plan);
        });
        return $planes;
    }


  private function getOperacionesGratis($plan)
    {
        // Obtener colección de precios disponible (precios, preciosVigentes o consulta directa)
        if ($plan->relationLoaded('precios')) {
            $precios = $plan->precios;
        } elseif ($plan->relationLoaded('preciosVigentes')) {
            $precios = $plan->preciosVigentes;
        } else {
            // como fallback, consultamos la relación precios
            $precios = $plan->precios()->get();
        }

        // Asegurar que sea una colección
        if (is_null($precios)) {
            $precios = collect([]);
        }

        // Buscar el tramo gratuito (precio == 0)
        $gratis = $precios->firstWhere('precio', 0);

        // Devolver hasta_factura (o 0 si no existe)
        return $gratis ? ($gratis->hasta_factura ?? 0) : 0;
    }
    public function getById($id): ?CatPlanes
    {
        return CatPlanes::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return CatPlanes::create($data);
    }

    public function update(array $data, $id)
    {
        $plan = CatPlanes::where('id', $id)->first();

        if ($plan) {
            $plan->update($data);
            return $plan;
        }

        return null;
    }
}
