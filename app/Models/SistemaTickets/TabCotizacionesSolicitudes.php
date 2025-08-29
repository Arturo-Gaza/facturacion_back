<?php

namespace App\Models\SistemaTickets;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabCotizacionesSolicitudes extends Model
{
    use HasFactory;
    protected $table = 'tab_cotizaciones_solicitudes';
    protected $fillable = [
        'id_solicitud',
        'id_usuario',
        'nombre_cotizacion',
        'archivo_cotizacion',
        'recomendada',
        'justificacion_general'
    ];

    public function solicitudDetalle()
    {
        return $this->belongsTo(TabSolicitud::class, 'id_solicitud');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

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

    protected $hidden = ['archivo_cotizacion'];
}
