<?php

namespace App\Models\SistemaTickets;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabSolicitud extends Model
{
    use HasFactory;
    protected $table = 'tab_solicitudes';
    protected $fillable = [
        'id_usuario_solicitud',
        'prioridad',
        'descripcion',
        'justificacion',
        'id_usuario_asignacion',
        'id_estatus_solicitud',
        'id_categoria',
        'justificacion_prioridad',
        'cotizacion_global',
        'prioridadModificada',
        'cotizadoGB'
    ];

    protected $hidden = [
        'usuarioAsignacion',
        'estatusSolicitud',
        'categoria',
        'archivosCotizaciones',
        'ultimaBitacora',
    ];
public function detalles()
{
    return $this->hasMany(TabSolicitudDetalle::class, 'id_solicitud')
                ->where('habilitado', true);
}

    public function usuarioSolicitud()
    {
        return $this->belongsTo(User::class, 'id_usuario_solicitud');
    }
    public function usuarioAsignacion()
    {
        return $this->belongsTo(User::class, 'id_usuario_asignacion');
    }


    public function estatusSolicitud()
    {
        return $this->belongsTo(CatEstatusSolicitud::class, 'id_estatus_solicitud');
    }
    public function categoria()
    {
        return $this->belongsTo(CatCategorias::class, 'id_categoria');
    }

    public function archivosCotizaciones()
    {
        return $this->hasMany(TabCotizacionesSolicitudes::class, 'id_solicitud');
    }
    public function observacionesReqInfo()
    {
        return $this->hasMany(TabObservacionesSolicitudReqInfo::class, 'id_solicitud');
    }

    protected $appends = [
        'tiene_archivos_cotizacion',
        'descripcion_departamento',
        'descripcion_tipo',
        'descripcion_estatus_solicitud',
        'user_solicitud',
        'user_asignacion',
        'descripcion_categoria',
        'prioridad_valor',
        'usuario_ultima_actualizacion',
        'observaciones_req_info'
    ];


    public function getArchivosCotizacionesAttribute()
    {
        return $this->archivosCotizaciones()->get();
    }
        public function getObservacionesReqInfoAttribute()
    {
        return $this->observacionesReqInfo()->get();
    }

    public function getDescripcionDepartamentoAttribute()
    {
        return optional($this->usuarioSolicitud->departamento)->descripcion ?? null;
    }
    public function getDescripcionTipoAttribute()
    {
        return optional($this->categoria->tipo)->descripcion ?? null;
    }


    public function getDescripcionCategoriaAttribute()
    {
        return optional($this->categoria)->descripcion_categoria;
    }

    public function getDescripcionEstatusSolicitudAttribute()
    {
        return optional($this->estatusSolicitud)->descripcion_estatus_solicitud;
    }
    public function getUserSolicitudAttribute()
    {
        return optional($this->usuarioSolicitud)->user;
    }
    public function getUserAsignacionAttribute()
    {
        return optional($this->usuarioAsignacion)->user;
    }
    public function getPrioridadValorAttribute()
    {
        $mapa = [
            //0 => 'Urgente',
            1 => 'Alta',
            2 => 'Media',
            3 => 'Baja',
            //99 => 'Extra urgente',

        ];


        return $mapa[$this->prioridad] ?? ""; // devuelve null si no encuentra la clave
    }
    public function ultimaBitacora()
    {
        return $this->hasOne(TabBitacoraSolicitud::class, 'id_solicitud')->latestOfMany();
    }

    public function getUsuarioUltimaActualizacionAttribute()
    {

        return optional(optional($this->ultimaBitacora)->usuario)->user ?? null;
    }

    public function getTieneArchivosCotizacionAttribute()
    {
        $existeGenerales = DB::table('tab_cotizaciones_solicitudes')
            ->where('id_solicitud', $this->id)
            ->exists();

        $existePorDetalle = DB::table('tab_cotizaciones_solicitud_detalle')
            ->whereIn('id_solicitud_detalle', function ($query) {
                $query->select('id')
                    ->from('tab_solicitud_detalle')
                    ->where('id_solicitud', $this->id);
            })
            ->exists();

        return $existeGenerales || $existePorDetalle;
    }
}
