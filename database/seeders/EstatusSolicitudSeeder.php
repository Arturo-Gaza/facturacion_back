<?php

namespace Database\Seeders;

use App\Models\SistemaTickets\CatEstatusSolicitud;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstatusSolicitudSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $estatus1 = new CatEstatusSolicitud();
        $estatus1->descripcion_estatus_solicitud = 'Capturada';
        $estatus1->mandarCorreo= false;
        $estatus1->save();

        $estatus2 = new CatEstatusSolicitud();
        $estatus2->descripcion_estatus_solicitud = 'Concluida';
        $estatus1->mandarCorreo= true;
        $estatus2->save();

        $estatus3 = new CatEstatusSolicitud();
        $estatus3->descripcion_estatus_solicitud = 'Cancelada';
        $estatus1->mandarCorreo= false;
        $estatus3->save();

    }
}
