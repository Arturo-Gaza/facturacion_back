<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatMotivoRechazo;

class CatMotivoRechazoSeeder extends Seeder
{
    public function run(): void
    {
        $motivos = [
            [
                'descripcion' => 'Fuera del mes vigencia',
                'detalle' => 'La fecha del ticket pertenece a un mes anterior al mes en curso',
                'activo' => true,
                'validar_por_IA' => true 
            ],
            [
                'descripcion' => 'Ilegible',
                'detalle' => 'El ticket no se puede leer o está dañado',
                'activo' => true,
                'validar_por_IA' => true
            ],
            [
                'descripcion' => 'Rechazado por el SAT',
                'detalle' => 'El ticket ha sido rechazado por el SAT',
                'activo' => true,
                'validar_por_IA' => false
            ],
            [
                'descripcion' => 'Inoperable tiempo',
                'detalle' => 'El ticket no puede ser procesado por tiempo',
                'activo' => true,
                'validar_por_IA' => false
            ],
            [
                'descripcion' => 'No es un ticket',
                'detalle' => 'El documento proporcionado no es un ticket válido, es decir no tiene datos correspondientes a un ticket',
                'activo' => true,
                'validar_por_IA' => true
            ],
            [
                'descripcion' => 'Falta información requerida',
                'detalle' => 'El ticket no contiene toda la información necesaria',
                'activo' => true,
                'validar_por_IA' => true
            ]
        ];

        foreach ($motivos as $motivo) {
            CatMotivoRechazo::updateOrCreate(
                ['descripcion' => $motivo['descripcion']],
                $motivo
            );
        }
    }
}
