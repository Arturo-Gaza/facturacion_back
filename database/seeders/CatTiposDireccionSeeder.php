<?php
// database/seeders/CatTiposDireccionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatTiposDireccionSeeder extends Seeder
{
    public function run()
    {
        $tipos = [
            ['clave' => 'FISCAL', 'descripcion' => 'Dirección fiscal para facturación'],
            ['clave' => 'ENVIO', 'descripcion' => 'Dirección para envío de mercancías'],
            ['clave' => 'OFICINA', 'descripcion' => 'Dirección de oficinas corporativas'],
            ['clave' => 'SUCURSAL', 'descripcion' => 'Dirección de sucursal'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('cat_tipos_direccion')->insert(array_merge($tipo, [
                'vigente' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}