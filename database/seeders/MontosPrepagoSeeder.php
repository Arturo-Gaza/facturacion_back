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
            [
                'id_plan'=>1,
                'nombre' => 'Básico',
                'descripcion' => 'Plan Básico con',
            
                'monto' => 1680.00,  
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                 'id_plan'=>1,
                'nombre' => 'Intermedio',
                'descripcion' => 'Plan Intermedio',
           
                'monto' => 3000.00,  
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                 'id_plan'=>1,
                'nombre' => 'Profesional',
                'descripcion' => 'Plan Profesional',
                
                'monto' => 4200.00, 
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('cat_montos_prepago')->insert($planes);
    }
}