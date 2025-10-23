<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatEmpresa extends Model
{
    use HasFactory;

    protected $table = 'cat_empresas';

    protected $fillable = [
        'rfc',
        'nombre_empresa',
        'pagina_web',
        'id_giro',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con el catálogo de giros
     */
    public function giro()
    {
        return $this->belongsTo(CatGiro::class, 'id_giro');
    }

    /**
     * Scope para empresas activas
     */
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para búsqueda
     */
    public function scopeBuscar($query, $search)
    {
        return $query->where('rfc', 'LIKE', "%{$search}%")
                    ->orWhere('nombre_empresa', 'LIKE', "%{$search}%")
                    ->orWhere('pagina_web', 'LIKE', "%{$search}%")
                    ->orWhereHas('giro', function($q) use ($search) {
                        $q->where('nombre', 'LIKE', "%{$search}%");
                    });
    }

    /**
     * Scope para filtrar por giro
     */
    public function scopePorGiro($query, $idGiro)
    {
        return $query->where('id_giro', $idGiro);
    }

    /**
     * Mutator para asegurar formato correcto del RFC
     */
    public function setRfcAttribute($value)
    {
        $this->attributes['rfc'] = strtoupper(trim($value));
    }

    /**
     * Accessor para el nombre de la empresa
     */
    public function getNombreEmpresaAttribute($value)
    {
        return ucwords(strtolower($value));
    }
}