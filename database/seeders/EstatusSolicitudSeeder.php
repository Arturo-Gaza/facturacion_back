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
                'color' => '#3498db', // Azul
                'color_cliente' => '#3498db'
            ],
            [
                'descripcion_estatus_solicitud' => 'En Revisión',
                'descripcion_cliente' => 'Procesando',
                'mandarCorreo' => false,
                'color' => '#f39c12', // Naranja
                'color_cliente' => '#f39c12'
            ],
            [
                'descripcion_estatus_solicitud' => 'Asignado',
                'descripcion_cliente' => 'Procesando',
                'mandarCorreo' => true,
                'color' => '#27ae60', // Verde
                'color_cliente' => '#f39c12'
            ],
            [
                'descripcion_estatus_solicitud' => 'Visualizado',
                'descripcion_cliente' => 'Procesando',
                'mandarCorreo' => false,
                'color' => '#9b59b6', // Morado
                'color_cliente' => '#f39c12'
            ],
            [
                'descripcion_estatus_solicitud' => 'Procesando',
                'descripcion_cliente' => 'Procesando',
                'mandarCorreo' => false,
                'color' => '#e67e22', // Naranja oscuro
                'color_cliente' => '#f39c12'
            ],
            [
                'descripcion_estatus_solicitud' => 'Recuperado',
                'descripcion_cliente' => 'Recuperado',
                'mandarCorreo' => false,
                'color' => '#2ecc71', // Verde claro
                'color_cliente' => '#2ecc71'
            ],
            [
                'descripcion_estatus_solicitud' => 'Rechazado',
                'descripcion_cliente' => 'Rechazado',
                'mandarCorreo' => false,
                'color' => '#e74c3c', // Rojo
                'color_cliente' => '#e74c3c'
            ],
            [
                'descripcion_estatus_solicitud' => 'Descargado',
                'descripcion_cliente' => 'Descargado',
                'mandarCorreo' => false,
                'color' => '#16a085', // Verde azulado
                'color_cliente' => '#16a085'
            ],
            [
                'descripcion_estatus_solicitud' => 'Concluido',
                'descripcion_cliente' => 'Concluido',
                'mandarCorreo' => false,
                'color' => '#2c3e50', // Azul oscuro
                'color_cliente' => '#2c3e50'
            ]
        ];

        foreach ($estatusData as $data) {
            CatEstatusSolicitud::create($data);
        }
    }
}