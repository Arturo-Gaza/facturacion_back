<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\UserEmail;
use App\Models\Catalogos\CatRoles;
use App\Models\SistemaTickets\CatDepartamentos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'idRol',
        'id_mail_principal', // Cambiado de 'email' a 'id_mail_principal'
        'id_departamento',
        'password',
        'usuario', // Cambiado de 'user' a 'usuario para coincidir con migración
        'habilitado',
        'intentos',
        'login_activo',
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
        ];
    }
    
    protected $appends = ['descripcion_rol','descripcio_depatamento', 'email'];
    
    // Nueva relación con el correo principal
    public function mailPrincipal()
    {
        return $this->belongsTo(UserEmail::class, 'id_mail_principal');
    }
    
    public function rol()
    {
        return $this->belongsTo(CatRoles::class, 'idRol');
    }
    
    public function departamento()
    {
        return $this->belongsTo(CatDepartamentos::class, 'id_departamento');
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
}
