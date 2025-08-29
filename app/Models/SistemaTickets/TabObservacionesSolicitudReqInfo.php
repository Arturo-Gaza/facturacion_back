<?php

namespace App\Models\SistemaTickets;

use App\Models\SistemaTickets\TabObservacionesSolicitudesDetalle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabObservacionesSolicitudReqInfo extends Model
{
    use HasFactory;

    protected $table = 'tab_observaciones_solicitud_req_info';

    protected $fillable = [
        'id_solicitud',
        'id_usuario',
        'observacion',
    ];

    protected $appends = ["archivos"];

        public function archivos()
    {
        return $this->hasMany(TabArchivosObservacionesSolicitudReqInfo::class, 'id_observacion_solicitud_req_info');
    }

    public function getArchivosAttribute()
    {

         return $this->archivos()->get();
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
}
