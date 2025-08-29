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


            $departamento4 = new CatDepartamentos();
            $departamento4->descripcion = 'Recursos Humanos';
            $departamento4->save();

            $departamento6 = new CatDepartamentos();
            $departamento6->descripcion = 'Compras';
            $departamento6->save();

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

            $usuarios = [
                [
                    'email' => 'jessika.padilla@alltub.com.mx',
                    'name' => 'Jessika Reyna',
                    'apellidoP' => 'Padilla',
                    'apellidoM' => 'Sanchez',
                    'user' => 'JessikaRP',
                    'idRol' => 3, // Usuario Compras
                ],
                [
                    'email' => 'paola.martinez@alltub.com.mx',
                    'name' => 'Paola Teresa',
                    'apellidoP' => 'Martinez',
                    'apellidoM' => 'Morales',
                    'user' => 'PaolaTM',
                    'idRol' => 3, // Usuario Compras
                ],
                [
                    'email' => 'reyna.ramirez@alltub.com.mx',
                    'name' => 'Reyna',
                    'apellidoP' => 'Ramírez',
                    'apellidoM' => 'Hernández',
                    'user' => 'ReynaRH',
                    'idRol' => 2, // Admin Compras
                ],
            ];

            $usuariosMap = [];

            foreach ($usuarios as $u) {
                $usuario = User::firstOrCreate(
                    ['email' => $u['email']],
                    [
                        'name' => $u['name'],
                        'apellidoP' => $u['apellidoP'],
                        'apellidoM' => $u['apellidoM'],
                        'user' => $u['user'],
                        'idRol' => $u['idRol'],
                        'id_departamento' => $departamento6->id, // temporal, luego se actualiza
                        'password' => bcrypt('P@ssword1'),
                        'habilitado' => true,
                        'intentos' => 0,
                        'login_activo' => false,
                    ]
                );

                $usuariosMap[$u['email']] = $usuario->id;
            }

            // Asociar departamentos con responsables
            $departamentoResponsables = [
                'Cadena de Suministros' => 'jessika.padilla@alltub.com.mx',
                'Tintas' => 'jessika.padilla@alltub.com.mx',
                'Recursos Humanos' => 'jessika.padilla@alltub.com.mx',
                'Calidad' => 'jessika.padilla@alltub.com.mx',
                'Servicios Internos' => 'jessika.padilla@alltub.com.mx',
                'Ventas' => 'jessika.padilla@alltub.com.mx',
                'Finanzas' => 'jessika.padilla@alltub.com.mx',
                'Dirección General' => 'jessika.padilla@alltub.com.mx',
                'Mantenimiento' => 'paola.martinez@alltub.com.mx',
                'Seguridad' => 'paola.martinez@alltub.com.mx',
                'Almacén de Refacciones' => 'paola.martinez@alltub.com.mx',
                'Mejora Continua' => 'paola.martinez@alltub.com.mx',
                'IT' => 'paola.martinez@alltub.com.mx',
                'Alltub México' => 'reyna.ramirez@alltub.com.mx',

            ];

            foreach ($departamentoResponsables as $nombre => $email) {
                $departamento = CatDepartamentos::firstOrCreate(['descripcion' => $nombre]);
                $departamento->update(['id_usuario_responsable_compras' => $usuariosMap[$email]]);
            }


            // Obtener todos los departamentos existentes
            $departamentosTodos = CatDepartamentos::all();

            // Obtener IDs de departamentos ya asignados
            $departamentosAsignados = array_keys($departamentoResponsables);

            // Asignar a Jessika los departamentos faltantes
            foreach ($departamentosTodos as $departamento) {
                if (!in_array($departamento->descripcion, $departamentosAsignados)) {
                    $departamento->update(['id_usuario_responsable_compras' => $usuariosMap['reyna.ramirez@alltub.com.mx']]);
                }
            }


            $users = new User();
            $users->idRol = 1;
            $users->name = "Admin";
            $users->apellidoP = "Admin";
            $users->apellidoM = "Admin";
            $users->email = "admin@alltub.com.mx";
            $users->id_departamento = CatDepartamentos::where('descripcion', 'IT')->value('id');
            $users->password = '$2y$12$bHqfPcMYy3GLmbxM5iF54eOTGofmXHHyzuSYgP4MCS1EWUF2wNQX6'; //P@ssword1
            $users->user = 'Administrador';
            $users->habilitado = true;
            $users->intentos = 0;
            $users->login_activo = false;
            $users->save();
        });
    }
}
