<?php

namespace Database\Seeders;

use App\Models\SistemaTickets\CatEstatusSolicitud;
use App\Models\SistemaTickets\CatTipos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $estatus1 = new CatTipos();
        $estatus1->descripcion = 'Material';
        $estatus1->req_marca_modelo = 1;
        $estatus1->save();

        $estatus2 = new CatTipos();
        $estatus2->descripcion = 'Servicios';
        $estatus2->req_marca_modelo = 0;
        $estatus2->save();
    }
}
