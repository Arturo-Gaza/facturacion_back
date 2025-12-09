<?php

namespace App\Models\SistemaTickets;

use App\Models\Catalogos\CatEstatus;
use App\Models\Solicitud;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabBitacoraSolicitud extends Model
{
    use HasFactory;
    protected $table = 'tab_bitacora_solicitud';
    protected $fillable = [
        'id_solicitud',
        'id_estatus',
        'id_usuario',
        'pdf_url_anterior' ,
        'xml_url_anterior' 
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function estatusSolicitud()
    {
        return $this->belongsTo(CatEstatusSolicitud::class, 'id_estatus');
    }
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    protected $appends = ['descripcion_estatus_solicitud', 'user'];


    public function getDescripcionEstatusSolicitudAttribute()
    {
        return optional($this->estatusSolicitud)->descripcion_estatus_solicitud;
    }
    public function getUserAttribute()
    {
        return optional($this->usuario)->user;
    }
}
