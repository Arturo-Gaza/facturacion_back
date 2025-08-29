<?php

namespace Database\Seeders;

use App\Models\SistemaTickets\CatDepartamentos;
use App\Models\SistemaTickets\TabDepartamentosCategorias;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TabDepartamentosCategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departamentos = CatDepartamentos::all();

        foreach ($departamentos as $depto) {
            TabDepartamentosCategorias::firstOrCreate([
                'id_departamento' => $depto->id,
                'id_categoria' => 7,
            ]);
        }
    }
}
