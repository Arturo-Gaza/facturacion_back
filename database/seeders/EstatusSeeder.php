<?php

namespace Database\Seeders;

use App\Models\Catalogos\CatEstatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Estatus1 = new CatEstatus();
        $Estatus1->nombre = "Cargado";
        $Estatus1->habilitado = true;
        $Estatus1->save();

        $Estatus2 = new CatEstatus();
        $Estatus2->nombre = "En conteo";
        $Estatus2->habilitado = true;
        $Estatus2->save();

        $Estatus3 = new CatEstatus();
        $Estatus3->nombre = "Cerrado";
        $Estatus3->habilitado = true;
        $Estatus3->save();

        $Estatus4 = new CatEstatus();
        $Estatus4->nombre = "Cancelado";
        $Estatus4->habilitado = true;
        $Estatus4->save();

        $Estatus5 = new CatEstatus();
        $Estatus5->nombre = "En pausa";
        $Estatus5->habilitado = true;
        $Estatus5->save();

        $Estatus6 = new CatEstatus();
        $Estatus6->nombre = "Asignado";
        $Estatus6->habilitado = true;
        $Estatus6->save();

        $Estatus7 = new CatEstatus();
        $Estatus7->nombre = "Conteos cerrados";
        $Estatus7->habilitado = true;
        $Estatus7->save();

        $Estatus8 = new CatEstatus();
        $Estatus8->nombre = "Sin asignar";
        $Estatus8->habilitado = true;
        $Estatus8->save();

        $Estatus9 = new CatEstatus();
        $Estatus9->nombre = "Carga cerrada";
        $Estatus9->habilitado = true;
        $Estatus9->save();

    }
}
