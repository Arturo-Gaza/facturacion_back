<?php
// database/seeders/CatEstatusesSatSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatEstatusesSatSeeder extends Seeder
{
    public function run()
    {
        $estatuses = [
            ['clave' => 'ACTIVO', 'descripcion' => 'Contribuyente activo'],
            ['clave' => 'SUSPENDIDO', 'descripcion' => 'Contribuyente suspendido'],
            ['clave' => 'BAJA', 'descripcion' => 'Contribuyente dado de baja'],
            ['clave' => 'NULO', 'descripcion' => 'RFC nulo'],
            ['clave' => 'PRECARGA', 'descripcion' => 'En proceso de validación'],
        ];

        foreach ($estatuses as $estatus) {
            DB::table('cat_estatuses_sat')->insert(array_merge($estatus, [
                'vigente' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}