<?php

namespace Database\Seeders;

use App\Models\SistemaTickets\CatMoneda;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class MonedaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = new CatMoneda();
        $users->clave_moneda = "MXN";
        $users->descripcion_moneda = "PESO MEXICANO";
        $users->save();

        $users = new CatMoneda();
        $users->clave_moneda = "USD";
        $users->descripcion_moneda = "DÓLAR AMERICANO";
        $users->save();

        $users = new CatMoneda();
        $users->clave_moneda = "EURO";
        $users->descripcion_moneda = "EURO";
        $users->save();
    }
}
