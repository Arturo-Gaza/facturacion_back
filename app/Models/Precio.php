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
        'servicio_id',
        'precio',
        'vigencia_desde',
        'vigencia_hasta'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'vigencia_desde' => 'date',
        'vigencia_hasta' => 'date',
    ];

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }
}