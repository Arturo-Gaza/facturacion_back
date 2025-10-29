<?php

namespace App\Models\SistemaTickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatMoneda extends Model
{
    use HasFactory;
    protected $table = 'cat_moneda';
    protected $primaryKey = 'id_moneda';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'clave_moneda',
        'descripcion_moneda',
        'habilitado',
    ];

    public function Producto()
    {
        return $this->belongsTo(CatMoneda::class,'id_moneda');
    }
}
