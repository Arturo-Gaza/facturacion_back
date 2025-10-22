<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstatusMovimiento extends Model
{
    use HasFactory;

    protected $table = 'cat_estatus_movimiento';

        protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function movimientosSaldo(): HasMany
    {
        return $this->hasMany(MovimientoSaldo::class, 'tipo_movimiento_id');
    }
}