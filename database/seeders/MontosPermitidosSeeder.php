<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MontosPermitidosSeeder extends Seeder
{
    public function run(): void
    {
        $montos = [50.00, 100.00, 200.00, 500.00, 1000.00];
        
        foreach ($montos as $monto) {
            DB::table('cat_montos_permitidos')->insert([
                'monto' => $monto,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}