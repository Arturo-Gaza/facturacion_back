<?php

namespace App\Models\SistemaTickets;

use App\Models\SistemaTickets\TabObservacionesSolicitudesDetalle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabArchivosObservacionesDetalle extends Model
{
    use HasFactory;

    protected $table = 'tab_archivos_observaciones_detalle';

    protected $fillable = [
        'id_observacion_detalle',
        'nombre',
        'archivo',
    ];

    protected $hidden = [
        'archivo',
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)
            ->timezone(config('app.timezone'))
            ->format('d-M-Y H:i');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)
            ->timezone(config('app.timezone'))
            ->format('d-M-Y H:i');
    }

}
