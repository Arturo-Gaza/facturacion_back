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

        $estatusData = [
            ['Cargado', false],
            ['En Revisión', false],
            ['Asignado', true],
            ['Visualizado', false],
            ['Procesando', false],
            ['Recuperado', false],
            ['Rechazado', false],
            ['Descargado', false],
            ['Concluido', false]
        ];

        foreach ($estatusData as $data) {
            $estatus = new CatEstatusSolicitud();
            $estatus->descripcion_estatus_solicitud = $data[0];
            $estatus->mandarCorreo = $data[1];
            $estatus->save();
        }
    }
}
