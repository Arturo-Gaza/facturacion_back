<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReversionSolicitud extends Model
{
    protected $table = 'reversion_solicitudes';

    
    protected $fillable = [
        'id_emplado',
        'id_solicitud',
        'estado',
    ];

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ---- Relaciones ----

    public function empleado()
    {
        return $this->belongsTo(User::class, 'id_emplado');
    }

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function tokens()
    {
        return $this->hasMany(ReversionToken::class, 'reversion_id');
    }

    public function ultimoToken()
    {
        return $this->hasOne(ReversionToken::class, 'reversion_id')->latestOfMany();
    }
}
