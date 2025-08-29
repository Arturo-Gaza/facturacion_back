<?php

namespace Database\Seeders;

use App\Models\SistemaTickets\CatCategorias;
use App\Models\SistemaTickets\CatEstatusSolicitud;
use App\Models\SistemaTickets\CatTipos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $estatus1 = new CatCategorias();
        $estatus1->descripcion_categoria = 'Papeleria';
        $estatus1->id_tipo = 1;
        $estatus1->save();

        $estatus2 = new CatCategorias();
        $estatus2->descripcion_categoria = 'Consumibles TI';
        $estatus2->id_tipo = 1;
        $estatus2->save();

        $estatus3 = new CatCategorias();
        $estatus3->descripcion_categoria = 'Pinturas';
        $estatus3->id_tipo = 1;
        $estatus3->save();

        $estatus1 = new CatCategorias();
        $estatus1->descripcion_categoria = 'Equipo de computo';
        $estatus1->id_tipo = 1;
        $estatus1->save();

        $estatus2 = new CatCategorias();
        $estatus2->descripcion_categoria = 'Capacitación';
        $estatus2->id_tipo = 2;
        $estatus2->save();

        $estatus3 = new CatCategorias();
        $estatus3->descripcion_categoria = 'Limpieza de maquina';
        $estatus3->id_tipo = 2;
        $estatus3->save();

        $estatus3 = new CatCategorias();
        $estatus3->descripcion_categoria = 'General';
        $estatus3->id_tipo = 1;
        $estatus3->save();

    }
}
