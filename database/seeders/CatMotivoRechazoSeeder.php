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
                'descripcion' => 'Fuera del horario permitido',
                'detalle' => 'La solicitud no puede procesarse en horas próximas al cierre mensual',
                'activo' => true,
                'validar_por_IA' => false
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
                'descripcion' => 'Inoperable por tiempo',
                'detalle' => 'No procede: Estamos en el período de cierre mensual (menos del tiempo límite establecido para fin de mes)',
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
