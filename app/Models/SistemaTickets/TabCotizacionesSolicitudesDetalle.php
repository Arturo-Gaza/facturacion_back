<?php

namespace App\Models\SistemaTickets;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabCotizacionesSolicitudesDetalle extends Model
{
    use HasFactory;
    protected $table = 'tab_cotizaciones_solicitud_detalle';
    protected $fillable = [
        'id_solicitud_detalle',
        'id_usuario',
        'nombre_cotizacion',
        'archivo_cotizacion',
        'recomendada',
        'justificacion'
    ];

    protected $hidden = ['archivo_cotizacion'];

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
    public function solicitudDetalle()
    {
        return $this->belongsTo(TabSolicitudDetalle::class, 'id_solicitud_detalle');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
