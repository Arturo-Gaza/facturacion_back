<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatPlanesSeeder extends Seeder
{
    public function run(): void
    {
        $planes = [
            [
                'nombre' => 'Plan Básico',
                'numero_perfiles' => 1,
                'precio_mensual' => 99.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Plan Estándar', 
                'numero_perfiles' => 3,
                'precio_mensual' => 199.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Plan Premium',
                'numero_perfiles' => 5,
                'precio_mensual' => 299.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Plan Empresarial',
                'numero_perfiles' => 10,
                'precio_mensual' => 499.00,
                'activo' => true,
            ]
        ];
        
        foreach ($planes as $plan) {
            DB::table('cat_planes')->insert(array_merge($plan, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
        
        $this->command->info('✅ Planes insertados correctamente: ' . count($planes) . ' planes creados');
    }
}