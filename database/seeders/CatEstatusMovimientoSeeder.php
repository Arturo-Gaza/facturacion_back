<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatEstatusMovimientoSeeder extends Seeder
{
    public function run()
    {
        DB::table('cat_estatus_movimiento')->insert([
            [
                'nombre' => 'Pendiente',
                'descripcion' => 'Movimiento creado, en espera de ser procesado.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'En proceso',
                'descripcion' => 'Movimiento en proceso de validación o ejecución.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Completado',
                'descripcion' => 'Movimiento realizado exitosamente.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Fallido',
                'descripcion' => 'Error al procesar el movimiento.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Cancelado',
                'descripcion' => 'Movimiento cancelado por el usuario o el sistema.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Revertido',
                'descripcion' => 'Movimiento revertido por ajuste o devolución.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Retenido',
                'descripcion' => 'Movimiento retenido temporalmente por validación o revisión manual.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
