<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CatGiro;

class CatGirosSeeder extends Seeder
{
    public function run()
    {
        $giros = [
            ['nombre' => 'Ropa'],
            ['nombre' => 'Línea Area'],
            ['nombre' => 'Restaurante'],
            ['nombre' => 'Panadería'],
            ['nombre' => 'Taxi'],
            ['nombre' => 'Gasolinería'],
            ['nombre' => 'Transporte'],
            ['nombre' => 'Estacionamiento'],
            ['nombre' => 'Casetas'],
            ['nombre' => 'Tienda'],
            ['nombre' => 'Línea Transporte'],
            ['nombre' => 'Gas'],
            ['nombre' => 'Hotel'],
            ['nombre' => 'Papelería'],
            ['nombre' => 'Zapatos'],
        ];

        foreach ($giros as $giro) {
            CatGiro::create($giro);
        }
    }
}