<?php

namespace App\Models\SistemaTickets;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabObservacionesSolicitudes extends Model
{
    use HasFactory;
    protected $table = 'tab_observaciones_solicitud';
    protected $fillable = [
        'id_solicitud',
        'id_usuario',
        'observacion'
    ];
    public function solicitud()
    {
        return $this->belongsTo(TabSolicitud::class, 'id_solicitud');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
