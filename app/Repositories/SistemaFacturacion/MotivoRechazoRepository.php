<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\MotivoRechazoRepositoryInterface;
use App\Models\CatMotivoRechazo;
use App\Models\MovimientoSaldo;

class MotivoRechazoRepository implements MotivoRechazoRepositoryInterface
{
    public function getAllActivo()
    {
        return CatMotivoRechazo::where('activo', true)->get()->all();
    }
}
