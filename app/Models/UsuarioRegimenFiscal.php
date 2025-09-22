<?php

namespace App\Models;

use App\Models\Catalogos\CatRegimenesFiscales;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use App\Models\DatosFiscal;
class UsuarioRegimenFiscal extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usuario_regimenes_fiscales';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_usuario',
        'id_regimen',
        'predeterminado'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'predeterminado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener el usuario asociado al régimen fiscal.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Obtener el régimen fiscal.
     */
    public function regimen(): BelongsTo
    {
        return $this->belongsTo(CatRegimenesFiscales::class, 'id_regimen', 'id_regimen');
    }

    /**
     * Scope para obtener el régimen predeterminado de un usuario.
     */
    public function scopePredeterminado($query)
    {
        return $query->where('predeterminado', true);
    }

    /**
     * Scope para obtener los regímenes de un usuario específico.
     */
    public function scopeDeUsuario($query, $userId)
    {
        return $query->where('id_usuario', $userId);
    }

    /**
     * Marcar este régimen como predeterminado y desmarcar los demás del usuario.
     */
    public function marcarComoPredeterminado(): void
    {
        DB::transaction(function () {
            // Desmarcar todos los regímenes del usuario
            self::where('id_usuario', $this->id_usuario)
                ->update(['predeterminado' => false]);

            // Marcar este como predeterminado
            $this->update(['predeterminado' => true]);

            // Actualizar también en datos_fiscales
            DatosFiscal::where('id_usuario', $this->id_usuario)
                ->update(['id_regimen_predeterminado' => $this->id]);
        });
    }
}