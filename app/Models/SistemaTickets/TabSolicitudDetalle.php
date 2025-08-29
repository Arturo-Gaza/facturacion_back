<?php

namespace App\Models\SistemaTickets;

use App\Models\ArchivoCarga\TabArchivoSolicitudDetalle;
use App\Models\SistemaTickets\TabObservacionesSolicitudesDetalle;
use App\Models\SistemaTickets\TabCotizacionesSolicitudesDetalle;
use App\Models\ArchivoConteo\TabConteo;
use App\Models\Catalogos\CatProductos;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabSolicitudDetalle extends Model
{
    use HasFactory;
    protected $table = 'tab_solicitud_detalle';
    protected $fillable = [
        'id_producto',
        'id_solicitud',
        'descripcion',
        'marca',
        'modelo',
        'cantidad',
        'observacion',
        'habilitado',
        'cotizado'
    ];



    public function producto()
    {
        return $this->belongsTo(CatProductos::class, 'id_producto');
    }
    public function solicitud()
    {
        return $this->belongsTo(TabSolicitud::class, 'id_solicitud');
    }
    public function archivos()
    {
        return $this->hasMany(TabArchivoSolicitudDetalle::class, 'id_solicitud_detalle');
    }

    public function observacionDetalle()
    {
        return $this->hasMany(TabObservacionesSolicitudesDetalle::class, 'id_solicitud_detalle');
    }

    public function archivosCotizaciones()
    {
        return $this->hasMany(TabCotizacionesSolicitudesDetalle::class, 'id_solicitud_detalle');
    }


    protected $appends = ['clave_producto','descripcion_producto', 'archivos', 'observacion_detalle', 'archivos_cotizaciones'];


    public function getArchivosAttribute()
    {
        return $this->archivos()->get(); // Devuelve colección de archivos relacionados
    }

    public function getObservacionDetalleAttribute()
    {
        return $this->observacionDetalle()->get();
    }
    public function getArchivosCotizacionesAttribute()
    {
        return $this->archivosCotizaciones()->get();
    }

    public function getDescripcionProductoAttribute()
    {
        return optional($this->producto)->descripcion_producto;
    }

        public function getClaveProductoAttribute()
    {
        return optional($this->producto)->clave_producto;
    }
}
