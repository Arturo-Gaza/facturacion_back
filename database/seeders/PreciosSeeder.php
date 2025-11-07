<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PreciosSeeder extends Seeder
{
    public function run(): void
    {
        $hoy = Carbon::today();

        $precios = [
            // Planes Personales (id_plan = 1)
            [
                'nombre_precio'   => 'Gratis',
                'id_plan'         => 1,
                'precio'          => 0.00,
                'desde_factura'   => 1,
                'hasta_factura'   => 10,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
            [
                'nombre_precio'   => 'Check in',
                'id_plan'         => 1,
                'precio'          => 30,
                'desde_factura'   => 11,
                'hasta_factura'   => null,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
            [
                'nombre_precio'   => 'Gratis',
                'id_plan'         => 2,
                'precio'          => 0,
                'desde_factura'   => 1,
                'hasta_factura'   => 15,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
            [
                'nombre_precio'   => 'Vuelo',
                'id_plan'         => 2,
                'precio'          => 15,
                'desde_factura'   => 16,
                'hasta_factura'   => null,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],

            // Planes Profesionales (id_plan = 2)
            [
                'nombre_precio'   => 'Gratis',
                'id_plan'         => 3,
                'precio'          => 0.00,
                'desde_factura'   => 1,
                'hasta_factura'   => 30,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
            [
                'nombre_precio'   => 'Primera Clase',
                'id_plan'         => 3,
                'precio'          => 15,
                'desde_factura'   => 31,
                'hasta_factura'   => null,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
        ];

        DB::table('precios')->insert($precios);
    }
}