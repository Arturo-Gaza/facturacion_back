<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatMotivoRechazo;

class CatMotivoRechazoSeeder extends Seeder
{
    public function run(): void
    {
        $motivos = [
            ['descripcion' => 'Fuera de vigencia'],
            ['descripcion' => 'Ilegible'],
            ['descripcion' => 'SAT'],
            ['descripcion' => 'Inoperable tiempo'],
        ];

        foreach ($motivos as $motivo) {
            CatMotivoRechazo::updateOrCreate($motivo);
        }
    }
}
