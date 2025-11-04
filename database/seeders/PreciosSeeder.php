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
                'nombre_precio'   => 'Personal Básico',
                'id_plan'         => 1,
                'precio'          => 0.00,
                'desde_factura'   => 1,
                'hasta_factura'   => 10,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
            [
                'nombre_precio'   => 'Personal Avanzado',
                'id_plan'         => 1,
                'precio'          => 11,
                'desde_factura'   => 51,
                'hasta_factura'   => 200,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
            [
                'nombre_precio'   => 'Personal Premium',
                'id_plan'         => 1,
                'precio'          => 10,
                'desde_factura'   => 201,
                'hasta_factura'   => 500,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
            [
                'nombre_precio'   => 'Personal Máximo',
                'id_plan'         => 1,
                'precio'          => 3,
                'desde_factura'   => 501,
                'hasta_factura'   => null,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],

            // Planes Profesionales (id_plan = 2)
            [
                'nombre_precio'   => 'Empresarial Básico',
                'id_plan'         => 2,
                'precio'          => 0.00,
                'desde_factura'   => 1,
                'hasta_factura'   => 100,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
            [
                'nombre_precio'   => 'Empresarial Pequeña',
                'id_plan'         => 2,
                'precio'          => 20,
                'desde_factura'   => 101,
                'hasta_factura'   => 500,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
            [
                'nombre_precio'   => 'Empresarial Mediana',
                'id_plan'         => 2,
                'precio'          => 15,
                'desde_factura'   => 501,
                'hasta_factura'   => 2000,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
            [
                'nombre_precio'   => 'Empresarial Grande',
                'id_plan'         => 2,
                'precio'          => 10,
                'desde_factura'   => 2001,
                'hasta_factura'   => 10000,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
            [
                'nombre_precio'   => 'Empresarial Corporativo',
                'id_plan'         => 2,
                'precio'          => 9,
                'desde_factura'   => 10001,
                'hasta_factura'   => null,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
        ];

        DB::table('precios')->insert($precios);
    }
}