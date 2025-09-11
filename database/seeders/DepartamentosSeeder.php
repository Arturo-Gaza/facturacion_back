<?php

namespace Database\Seeders;

use App\Models\SistemaTickets\CatDepartamentos;
use App\Models\SistemaTickets\CatEstatusSolicitud;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {



        DB::transaction(function () {




            $nuevosDepartamentos = [
                'Mantenimiento',
                'Ventas',
                'Producción',
                'Calidad',
                'Cadena de Suministros',
                'Finanzas',
                'Dirección General',
                'IT',
                'Inspección de Procesos',
                'Logística',
                'Ingeniería',
                'Dirección',
                'Almacén de Materia Prima',
                'Tintas',
                'Servicios Internos',
                'Seguridad',
                'Almacén de Refacciones',
                'Mejora Continua',
                'Alltub México',
            ];

            foreach ($nuevosDepartamentos as $nombre) {
                CatDepartamentos::firstOrCreate(['descripcion' => $nombre]);
            }

        });
    }
}
