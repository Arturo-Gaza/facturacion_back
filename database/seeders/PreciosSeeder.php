<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PreciosSeeder extends Seeder
{
    public function run(): void
    {
        $idPlan = 1; // Ajusta según tu tabla cat_planes
        $hoy = Carbon::today();

        $precios = [
            [
                'nombre_precio'   => 'Gratis',
                'id_plan'         => $idPlan,
                'precio'          => 0.00,
                'desde_factura'   => 1,
                'hasta_factura'   => 10,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
            [
                'nombre_precio'   => 'Bronce',
                'id_plan'         => $idPlan,
                'precio'          => 199.99,
                'desde_factura'   => 11,
                'hasta_factura'   => 100,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
            [
                'nombre_precio'   => 'Plata',
                'id_plan'         => $idPlan,
                'precio'          => 149.99,
                'desde_factura'   => 101,
                'hasta_factura'   => 500,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
            [
                'nombre_precio'   => 'Oro',
                'id_plan'         => $idPlan,
                'precio'          => 99.99,
                'desde_factura'   => 501,
                'hasta_factura'   => null,
                'vigencia_desde'  => $hoy,
                'vigencia_hasta'  => null,
            ],
        ];

        DB::table('precios')->insert($precios);
    }
}
