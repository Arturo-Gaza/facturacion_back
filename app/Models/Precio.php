<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Precio extends Model
{
    use HasFactory;

    protected $table = 'precios';

    protected $fillable = [
        'id_plan',
        'nombre_precio',
        'precio',
        'vigencia_desde',
        'vigencia_hasta',
        'desde_factura',
        'hasta_factura'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'vigencia_desde' => 'date',
        'vigencia_hasta' => 'date',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(CatPlanes::class);
    }
}