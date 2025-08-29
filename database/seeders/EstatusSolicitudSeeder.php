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
        $estatus2->descripcion_estatus_solicitud = 'Enviada';
        $estatus1->mandarCorreo= true;
        $estatus2->save();

        $estatus3 = new CatEstatusSolicitud();
        $estatus3->descripcion_estatus_solicitud = 'Cancelada';
        $estatus1->mandarCorreo= false;
        $estatus3->save();

        $estatus4 = new CatEstatusSolicitud();
        $estatus4->descripcion_estatus_solicitud = 'Concluida';
        $estatus1->mandarCorreo= true;
        $estatus4->save();

        $estatus5 = new CatEstatusSolicitud();
        $estatus5->descripcion_estatus_solicitud = 'Requiere información';
        $estatus1->mandarCorreo= true;
        $estatus5->save();

        $estatus5 = new CatEstatusSolicitud();
        $estatus5->descripcion_estatus_solicitud = 'Respuesta al requerimiento';
        $estatus1->mandarCorreo= false;
        $estatus5->save();

        $estatus6 = new CatEstatusSolicitud();
        $estatus6->descripcion_estatus_solicitud = 'Vizualizada';
        $estatus1->mandarCorreo= false;
        $estatus6->save();

        $estatus6 = new CatEstatusSolicitud();
        $estatus6->descripcion_estatus_solicitud = 'Cotizando';
        $estatus1->mandarCorreo= false;
        $estatus6->save();

        $estatus6 = new CatEstatusSolicitud();
        $estatus6->descripcion_estatus_solicitud = 'Cotizada';
        $estatus1->mandarCorreo= false;
        $estatus6->save();

        $estatus6 = new CatEstatusSolicitud();
        $estatus6->descripcion_estatus_solicitud = 'Recibida';
        $estatus1->mandarCorreo= false;
        $estatus6->save();
    }
}
