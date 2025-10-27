<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SolicitudDatoAdicional extends Model
{
    use HasFactory;

    protected $table = 'solicitud_dato_adicional';

    protected $fillable = [
        'id_solicitud',
        'etiqueta',
        'valor'
    ];


    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }
}
