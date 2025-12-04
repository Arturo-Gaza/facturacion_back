<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReversionToken extends Model
{
    protected $table = 'reversion_tokens';

    protected $fillable = [
        'reversion_id',
        'token',
        'created_por_admin',
        'used',
        'used_at',
    ];

    // Laravel manejará created_at automáticamente
    // PERO no hay updated_at, así que lo desactivamos
    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
        'used_at' => 'datetime',
        'used' => 'boolean',
    ];

    // ---- Relaciones ----

    public function solicitud()
    {
        return $this->belongsTo(ReversionSolicitud::class, 'reversion_id');
    }

    public function administrador()
    {
        return $this->belongsTo(User::class, 'created_por_admin');
    }
}
