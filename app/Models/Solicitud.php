<?php

namespace App\Models;

use App\Models\Catalogos\CatRegimenesFiscales;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use App\Models\SistemaTickets\CatEstatusSolicitud;
use App\Models\SistemaTickets\TabBitacoraSolicitud;
use Exception;
use Illuminate\Support\Facades\Storage;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'num_ticket',
        'usuario_id',
        'imagen_url',
        'texto_ocr',
        'texto_json',
        'monto',
        'establecimiento',
        'empleado_id',
        'estado_id',
        'id_receptor',
        'usoCFDI',
        'id_usuario_asignacion',
        'url_facturacion'
    ];
    protected $appends = [
        'clave'
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
        return $this->belongsTo(CatEstatusSolicitud::class, 'estado_id');
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }
    /**
     * Manejo de la carga de archivos
     */
    public function guardarImagen(UploadedFile $archivo,$usuarioId, string $carpeta = 'solicitudes'): string
    {
        
        if (!$usuarioId) {
            throw new Exception('Usuario no autenticado');
        }
         $carpetaUsuario = $carpeta . '/usuario_' . $usuarioId;
        // Generar hash del contenido del archivo
        $hash = md5_file($archivo->getPathname());

        // Verificar si ya existe un archivo con el mismo hash
          $archivosExistentes = Storage::disk('public')->files($carpetaUsuario);
        // Eliminar imagen anterior si existe
        if ($this->imagen_url && Storage::exists($this->imagen_url)) {
            Storage::delete($this->imagen_url);
        }
        foreach ($archivosExistentes as $archivoExistente) {
            $hashExistente = md5_file(Storage::disk('public')->path($archivoExistente));
            if ($hash === $hashExistente) {
                throw new \Exception('El archivo ya existe en el sistema');
            }
        }


        // Guardar nueva imagen
        $ruta = $archivo->store($carpetaUsuario, 'public');

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
    public function receptor()
    {
        return $this->belongsTo(DatosFiscal::class, 'id_receptor');
    }
    public function usoCfdi()
    {
        return $this->belongsTo(CatUsoCfdi::class, 'uso_cfdi_id');
    }

    public function regimen()
    {
        return $this->belongsTo(CatRegimenesFiscales::class, 'id_regimen', 'id_regimen');
    }
    public function getClaveAttribute()
    {
        return $this->regimen?->clave; // Asumiendo que la columna se llama 'clave'
    }
    public function bitacora(): HasMany
    {
        return $this->hasMany(TabBitacoraSolicitud::class, 'id_solicitud');
    }
}
