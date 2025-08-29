<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Catalogos\CatRoles;
use App\Models\SistemaTickets\CatDepartamentos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'idRol',
        'name',
        'apellidoP',
        'apellidoM',
        'email',
        'id_departamento',
        'password',
        'user',
        'habilitado',
        'intentos',
        'login_activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    protected $appends = ['descripcion_rol','descripcio_depatamento'];
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
}
