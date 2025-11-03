<?php


namespace App\Models;

use Carbon\Carbon;
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

     public function scopeVigente($query, $date = null)
    {
        $date = $date ? Carbon::parse($date)->startOfDay() : Carbon::now()->startOfDay();

        return $query->where(function ($q) use ($date) {
            $q->where('vigencia_desde', '<=', $date)
              ->where(function ($q2) use ($date) {
                  $q2->where('vigencia_hasta', '>=', $date)
                     ->orWhereNull('vigencia_hasta');
              });
        });
    }
}