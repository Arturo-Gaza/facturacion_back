<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile; 

use Illuminate\Support\Facades\Storage;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'usuario_id',
        'imagen_url',
        'texto_ocr',
        'empleado_id',
        'estado_id'
    ];

    protected $casts = [
        'estado' => 'string',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'empleado_id');
    }

    public function estadoSolicitud(): BelongsTo
    {
        return $this->belongsTo(EstadoSolicitud::class, 'estado_id');
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }
    /**
     * Manejo de la carga de archivos
     */
    public function guardarImagen(UploadedFile $archivo, string $carpeta = 'solicitudes'): string
    {
        // Eliminar imagen anterior si existe
        if ($this->imagen_url && Storage::exists($this->imagen_url)) {
            Storage::delete($this->imagen_url);
        }

        // Guardar nueva imagen
        $ruta = $archivo->store($carpeta, 'public');
        
        return $ruta;
    }

    /**
     * Obtener URL completa de la imagen
     */
    public function getImagenUrlAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        return Storage::disk('public')->url($value);
    }

    /**
     * Obtener ruta del archivo en storage
     */
    public function getRutaImagenAttribute(): ?string
    {
        if (!$this->imagen_url) {
            return null;
        }

        return Storage::disk('public')->path($this->getRawOriginal('imagen_url'));
    }

    /**
     * Eliminar imagen asociada
     */
    public function eliminarImagen(): void
    {
        if ($this->imagen_url && Storage::exists($this->getRawOriginal('imagen_url'))) {
            Storage::delete($this->getRawOriginal('imagen_url'));
        }
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopePorEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para solicitudes de un usuario específico
     */
    public function scopeDeUsuario($query, int $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }
}