<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\UserPhone;
use App\Models\UserEmail;
use App\Models\CatEstatusUsuario;
use App\Models\Catalogos\CatRoles;
use App\Models\SistemaTickets\CatDepartamentos;
use App\Models\DatosFiscal; // Agrega esta importación
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'idRol',
        'id_mail_principal',
        'id_telefono_principal',
        'id_departamento',
        'password',
        'id_estatus_usuario',
        'intentos',
        'login_activo',
        'saldo',
        'datos_fiscales_principal',
        'datos_fiscales_personal',
        'usuario_padre',
        'password_temporal',
        'id_plan',
        'vigencia_plan_inicio',
        'vigencia_plan_fin'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'saldo' => 'decimal:2', // Cast para el saldo
        ];
    }

    protected $appends = [
        'descripcion_rol',
        'descripcio_depatamento',
        'email',
        'phone',
        'nombre',
        'apellido_paterno',
        'apellido_materno'
    ];

    public function estatusUsuario()
    {
        return $this->belongsTo(CatEstatusUsuario::class, 'id_estatus_usuario');
    }
    // Nueva relación con el correo principal
    public function mailPrincipal()
    {
        return $this->belongsTo(UserEmail::class, 'id_mail_principal');
    }

    public function telefonoPrincipal()
    {
        return $this->belongsTo(UserPhone::class, 'id_telefono_principal');
    }

    public function rol()
    {
        return $this->belongsTo(CatRoles::class, 'idRol');
    }

    public function departamento()
    {
        return $this->belongsTo(CatDepartamentos::class, 'id_departamento');
    }

    // Relación con los datos fiscales principales
    public function datosFiscalesPrincipal()
    {
        return $this->belongsTo(DatosFiscal::class, 'datos_fiscales_principal');
    }
    public function datosFiscalesPersonal()
    {
        return $this->belongsTo(DatosFiscal::class, 'datos_fiscales_personal');
    }
    public function direccionPersonal()
    {
        return $this->hasOneThrough(
            Direccion::class,
            DatosFiscal::class,
            'id', // Foreign key on DatosFiscal table
            'id_fiscal', // Foreign key on Direccion table  
            'datos_fiscales_personal', // Local key on User table
            'id' // Local key on DatosFiscal table
        )->where('direcciones.id_tipo_direccion', 2);
    }

    public function getDescripcionRolAttribute()
    {
        return optional($this->rol)->nombre;
    }

    public function getDescripcioDepatamentoAttribute()
    {
        return optional($this->departamento)->descripcion;
    }

    // Accessor para mantener compatibilidad con email
    public function getEmailAttribute()
    {
        return optional($this->mailPrincipal)->email;
    }

    public function getPhoneAttribute()
    {
        return optional($this->telefonoPrincipal)->telefono;
    }

    // Accessors para los datos personales desde datos fiscales principales
    public function getNombreAttribute()
    {
        $nombre = optional($this->datosFiscalesPersonal)->nombre_razon;

        return $nombre;
    }

    public function getApellidoPaternoAttribute()
    {
        return optional($this->datosFiscalesPersonal)->apellido_paterno;
    }

    public function getApellidoMaternoAttribute()
    {
        return optional($this->datosFiscalesPersonal)->apellido_materno;
    }

    public function emails()
    {
        return $this->hasMany(UserEmail::class);
    }

    public function phones()
    {
        return $this->hasMany(UserPhone::class);
    }


    /**
     * Obtener los datos fiscales del usuario.
     */
    public function datosFiscales()
    {
        return $this->hasOne(DatosFiscal::class, 'id_usuario');
    }
    /**
     * Scope para usuarios activos
     */
    public function scopeActivos($query)
    {
        return $query->whereHas('estatusUsuario', function ($q) {
            $q->where('clave', 'activo');
        });
    }

    /**
     * Scope para usuarios bloqueados
     */
    public function scopeBloqueados($query)
    {
        return $query->whereHas('estatusUsuario', function ($q) {
            $q->where('clave', 'bloqueado');
        });
    }

    /**
     * Verificar si el usuario está activo
     */
    public function getEstaActivoAttribute()
    {
        return $this->estatusUsuario && $this->estatusUsuario->clave === 'activo';
    }

    /**
     * Verificar si el usuario está bloqueado
     */
    public function getEstaBloqueadoAttribute()
    {
        return $this->estatusUsuario && $this->estatusUsuario->clave === 'bloqueado';
    }

    /**
     * Verificar si el usuario está eliminado
     */
    public function getEstaEliminadoAttribute()
    {
        return $this->estatusUsuario && $this->estatusUsuario->clave === 'eliminado';
    }
    public function facturantesPermitidos()
    {
        return $this->belongsToMany(DatosFiscal::class, 'usuario_hijo_facturantes', 'id_usuario_hijo', 'id_dato_fiscal')
            ->withPivot('predeterminado')
            ->withTimestamps();
    }
    public function planVencido()
    {
        return $this->vigencia_plan_fin && $this->vigencia_plan_fin < now();
    }

    public function suscripcionActiva(): HasOne
    {
        return $this->hasOne(Suscripciones::class, 'usuario_id')
            ->where('estado', 'activa')
            ->where('fecha_vencimiento', '>=', now())
            ->latest();
    }

    /**
     * Verificar si el usuario tiene suscripción activa
     */
    public function tieneSuscripcionActiva(): bool
    {
        return $this->suscripcionActiva !== null;
    }
}
