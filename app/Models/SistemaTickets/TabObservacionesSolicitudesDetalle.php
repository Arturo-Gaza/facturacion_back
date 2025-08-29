<?php

namespace App\Models\SistemaTickets;

use App\Models\SistemaTickets\TabArchivosObservacionesDetalle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabObservacionesSolicitudesDetalle extends Model
{
    use HasFactory;
    protected $table = 'tab_observaciones_solicitud_detalle';
    protected $fillable = [
        'id_solicitud_detalle',
        'id_usuario',
        'observacion'
    ];

 protected $appends = ["archivos"];

    public function solicitudDetalle()
    {
        return $this->belongsTo(TabSolicitudDetalle::class, 'id_solicitud_detalle');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

        public function archivos()
    {
        return $this->hasMany(TabArchivosObservacionesDetalle::class, 'id_observacion_detalle');
    }

    public function getArchivosAttribute()
    {

         return $this->archivos()->get();
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

}
