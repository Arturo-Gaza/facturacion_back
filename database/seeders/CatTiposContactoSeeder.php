<?php
// database/seeders/CatTiposContactoSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatTiposContactoSeeder extends Seeder
{
    public function run()
    {
        $tipos = [
            ['clave' => 'EMAIL', 'descripcion' => 'Correo electrónico'],
            ['clave' => 'TELEFONO', 'descripcion' => 'Teléfono fijo'],
            ['clave' => 'MOVIL', 'descripcion' => 'Teléfono móvil'],
            ['clave' => 'FAX', 'descripcion' => 'Fax'],
            ['clave' => 'WEB', 'descripcion' => 'Sitio web'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('cat_tipos_contacto')->insert(array_merge($tipo, [
                'vigente' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}