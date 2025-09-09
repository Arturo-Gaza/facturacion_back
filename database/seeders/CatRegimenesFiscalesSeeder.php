<?php
// database/seeders/CatRegimenesFiscalesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatRegimenesFiscalesSeeder extends Seeder
{
    public function run()
    {
        $regimenes = [
            ['clave' => '601', 'descripcion' => 'General de Ley Personas Morales', 'aplica_pf' => false, 'aplica_pm' => true],
            ['clave' => '603', 'descripcion' => 'Personas Morales con Fines no Lucrativos', 'aplica_pf' => false, 'aplica_pm' => true],
            ['clave' => '605', 'descripcion' => 'Sueldos y Salarios e Ingresos Asimilados a Salarios', 'aplica_pf' => true, 'aplica_pm' => false],
            ['clave' => '606', 'descripcion' => 'Arrendamiento', 'aplica_pf' => true, 'aplica_pm' => false],
            ['clave' => '607', 'descripcion' => 'Régimen de Enajenación o Adquisición de Bienes', 'aplica_pf' => true, 'aplica_pm' => false],
            ['clave' => '608', 'descripcion' => 'Demás ingresos', 'aplica_pf' => true, 'aplica_pm' => false],
            ['clave' => '610', 'descripcion' => 'Residentes en el Extranjero sin Establecimiento Permanente en México', 'aplica_pf' => true, 'aplica_pm' => true],
            ['clave' => '611', 'descripcion' => 'Ingresos por Dividendos (socios y accionistas)', 'aplica_pf' => true, 'aplica_pm' => false],
            ['clave' => '612', 'descripcion' => 'Personas Físicas con Actividades Empresariales y Profesionales', 'aplica_pf' => true, 'aplica_pm' => false],
            ['clave' => '614', 'descripcion' => 'Ingresos por intereses', 'aplica_pf' => true, 'aplica_pm' => false],
            ['clave' => '616', 'descripcion' => 'Sin obligaciones fiscales', 'aplica_pf' => true, 'aplica_pm' => false],
            ['clave' => '620', 'descripcion' => 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos', 'aplica_pf' => false, 'aplica_pm' => true],
            ['clave' => '621', 'descripcion' => 'Incorporación Fiscal', 'aplica_pf' => true, 'aplica_pm' => false],
            ['clave' => '622', 'descripcion' => 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras', 'aplica_pf' => true, 'aplica_pm' => true],
            ['clave' => '623', 'descripcion' => 'Opcional para Grupos de Sociedades', 'aplica_pf' => false, 'aplica_pm' => true],
            ['clave' => '624', 'descripcion' => 'Coordinados', 'aplica_pf' => false, 'aplica_pm' => true],
            ['clave' => '628', 'descripcion' => 'Hidrocarburos', 'aplica_pf' => true, 'aplica_pm' => true],
            ['clave' => '629', 'descripcion' => 'De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales', 'aplica_pf' => false, 'aplica_pm' => true],
            ['clave' => '630', 'descripcion' => 'Enajenación de acciones en bolsa de valores', 'aplica_pf' => true, 'aplica_pm' => false],
        ];

        foreach ($regimenes as $regimen) {
            DB::table('cat_regimenes_fiscales')->insert(array_merge($regimen, [
                'habilitado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}