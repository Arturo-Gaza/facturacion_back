<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\UserPhone;
use App\Models\UserEmail;
use App\Models\Catalogos\CatRoles;
use App\Models\SistemaTickets\CatDepartamentos;
use App\Models\DatosFiscal; // Agrega esta importación
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'habilitado',
        'intentos',
        'login_activo',
        'saldo',
        'datos_fiscales_principal',
        'datos_fiscales_personal',
        'usuario_padre', // Agrega este campo si lo necesitas
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
        return optional($this->datosFiscalesPrincipal)->nombre;
    }

    public function getApellidoPaternoAttribute()
    {
        return optional($this->datosFiscalesPrincipal)->apellido_paterno;
    }

    public function getApellidoMaternoAttribute()
    {
        return optional($this->datosFiscalesPrincipal)->apellido_materno;
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
}
