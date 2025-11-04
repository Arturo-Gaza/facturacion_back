<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MontosPrepagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $planes = [
            // Planes Personales (id_plan = 1)
            [
                'id_plan' => 1,
                'nombre' => 'Personal Básico',
                'descripcion' => 'Ideal para usuarios individuales y emprendedores',
                'monto' => 499.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 1,
                'nombre' => 'Personal Avanzado',
                'descripcion' => 'Para freelancers y profesionales independientes',
                'monto' => 899.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 1,
                'nombre' => 'Personal Premium',
                'descripcion' => 'Máximas funciones para uso personal',
                'monto' => 1299.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Planes Profesionales (id_plan = 2)
            [
                'id_plan' => 2,
                'nombre' => 'Profesional StartUp',
                'descripcion' => 'Perfecto para pequeñas empresas y startups',
                'monto' => 2499.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 2,
                'nombre' => 'Profesional Business',
                'descripcion' => 'Para empresas en crecimiento con necesidades avanzadas',
                'monto' => 4499.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 2,
                'nombre' => 'Profesional Enterprise',
                'descripcion' => 'Solución completa para grandes organizaciones',
                'monto' => 7999.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('cat_montos_prepago')->insert($planes);
    }
}
