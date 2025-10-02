<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatEstatusUsuario extends Model
{
    protected $table = 'cat_estatus_usuario';

    protected $fillable = [
        'clave',
        'descripcion', 
        'habilitado'
    ];

    protected $casts = [
        'habilitado' => 'boolean'
    ];

    /**
     * Relación con usuarios
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'id_estatus_usuario');
    }
}