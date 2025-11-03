<?php

namespace Database\Seeders;

use App\Models\SistemaTickets\CatEstatusSolicitud;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstatusSolicitudSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estatusData = [
            [
                'descripcion_estatus_solicitud' => 'Cargado',
                'descripcion_cliente' => 'Cargado',
                'mandarCorreo' => false,
                'color' => 'bg-blue-600', // Azul
                'color_cliente' => 'bg-blue-600'
            ],
            [
                'descripcion_estatus_solicitud' => 'En Revisión',
                'descripcion_cliente' => 'Procesando',
                'mandarCorreo' => false,
                'color' => 'bg-yellow-400', // Naranja
                'color_cliente' => 'bg-yellow-400'
            ],
            [
                'descripcion_estatus_solicitud' => 'Asignado',
                'descripcion_cliente' => 'Procesando',
                'mandarCorreo' => true,
                'color' => 'bg-purple-500', // Verde
                'color_cliente' => 'bg-purple-500'
            ],
            [
                'descripcion_estatus_solicitud' => 'Visualizado',
                'descripcion_cliente' => 'Procesando',
                'mandarCorreo' => false,
                'color' => 'bg-green-400', // Morado
                'color_cliente' => 'bg-green-400'
            ],
            [
                'descripcion_estatus_solicitud' => 'Procesando',
                'descripcion_cliente' => 'Procesando',
                'mandarCorreo' => false,
                'color' => 'bg-orange-600', // Naranja oscuro
                'color_cliente' => 'bg-orange-600'
            ],
            [
                'descripcion_estatus_solicitud' => 'Recuperado',
                'descripcion_cliente' => 'Recuperado',
                'mandarCorreo' => false,
                'color' => 'bg-teal-500', // Verde claro
                'color_cliente' => 'bg-teal-500'
            ],
            [
                'descripcion_estatus_solicitud' => 'Rechazado',
                'descripcion_cliente' => 'Rechazado',
                'mandarCorreo' => false,
                'color' => 'bg-red-600', // Rojo
                'color_cliente' => 'bg-red-600'
            ],
            [
                'descripcion_estatus_solicitud' => 'Descargado',
                'descripcion_cliente' => 'Descargado',
                'mandarCorreo' => false,
                'color' => 'bg-indigo-500', // Verde azulado
                'color_cliente' => 'bg-indigo-500'
            ],
            [
                'descripcion_estatus_solicitud' => 'Concluido',
                'descripcion_cliente' => 'Concluido',
                'mandarCorreo' => false,
                'color' => 'bg-emerald-600', // Azul oscuro
                'color_cliente' => 'bg-emerald-600'
            ],
            [
                'descripcion_estatus_solicitud' => 'En espera de Archivos',
                'descripcion_cliente' => 'Procesando',
                'mandarCorreo' => false,
                'color' => 'bg-emerald-600', // Azul oscuro
                'color_cliente' => 'bg-emerald-600'
            ],
        ];

        foreach ($estatusData as $data) {
            CatEstatusSolicitud::create($data);
        }
    }
}
