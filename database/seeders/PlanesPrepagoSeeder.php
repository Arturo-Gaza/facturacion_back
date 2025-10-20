<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanesPrepagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $planes = [
            [
                'nombre' => 'Básico',
                'descripcion' => 'Plan Básico con 40 créditos',
                'creditos' => 40,
                'monto' => 1680.00, // 40 créditos * $42.00 MXN
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Intermedio',
                'descripcion' => 'Plan Intermedio con 80 créditos',
                'creditos' => 80,
                'monto' => 3000.00, // 80 créditos * $37.50 MXN
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Profesional',
                'descripcion' => 'Plan Profesional con 120 créditos',
                'creditos' => 120,
                'monto' => 4200.00, // 120 créditos * $35.00 MXN
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('cat_planes_prepago')->insert($planes);
    }
}