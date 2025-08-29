<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatProductoLiteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'clave_producto' => $this->clave_producto,
            'descripcion_producto' => $this->descripcion_producto,
            'descripcion_categoria' => optional($this->categoria)->descripcion_categoria,
            'descripcion_unidad' => optional($this->unidadMedida)->descripcion_unidad_medida ?? '',
            'descripcion_moneda' => optional($this->moneda)->descripcion_moneda ?? '',
            'descripcion_grupo' => optional($this->grupoFamilia)->descripcion_gpo_familia ?? '',
        ];
    }
}
