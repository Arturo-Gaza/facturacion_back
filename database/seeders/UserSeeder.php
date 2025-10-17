<?php

namespace Database\Seeders;

use App\Models\DatosFiscal;
use App\Models\DatosFiscalRegimenFiscal;
use App\Models\DatosFiscalRegimenUsoCfdi;
use App\Models\Direccion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\UserEmail;
use Illuminate\Support\Facades\DB;
use App\Models\SistemaTickets\CatDepartamentos;
use App\Models\UserPhone;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {

            // ============================
            // USUARIO ADMINISTRADOR (ROL 1)
            // ============================

            $user = User::create([
                'idRol' => 1,
                'password' => '$2y$12$bHqfPcMYy3GLmbxM5iF54eOTGofmXHHyzuSYgP4MCS1EWUF2wNQX6',
                'intentos' => 0,
                'login_activo' => false,
                'id_mail_principal' => null,
            ]);

            $emailAdmin = UserEmail::create([
                'user_id' => $user->id,
                'email' => 'admin@hopewellsystem.com',
                'verificado' => true,
            ]);

            $phoneAdmin = UserPhone::create([
                'user_id' => $user->id,
                'telefono' => '5551234567',
                'verificado' => true,
            ]);

            $user->update([
                'id_mail_principal' => $emailAdmin->id,
                'id_telefono_principal' => $phoneAdmin->id,
            ]);

            $datosFiscalesPersonal = DatosFiscal::create([
                'id_usuario' => $user->id,
                'nombre_razon' => 'Administrador',
                'primer_apellido' => 'Pérez',
                'segundo_apellido' => 'Gómez',
                'es_persona_moral' => false,
                'rfc' => 'PEGJ800101ABC',
                'curp' => 'PEGJ800101HDFRMN09',
                'lugar_emision' => 'Ciudad de México',
                'fecha_emision' => '2020-01-01',
                'fecha_inicio_op' => '2020-01-01',
                'id_estatus_sat' => 1,
                'email_facturacion_id' => $emailAdmin->id,
                'habilitado' => true,
            ]);

            $datosFiscalesPrincipal = DatosFiscal::create([
                'id_usuario' => $user->id,
                'nombre_razon' => 'HopeWell Systems S.A. de C.V.',
                'nombre_comercial' => 'HopeWell Systems',
                'es_persona_moral' => true,
                'rfc' => 'HWS120101ABC',
                'idCIF' => 'CIF123456789',
                'lugar_emision' => 'Ciudad de México',
                'fecha_emision' => '2012-01-01',
                'fecha_inicio_op' => '2012-01-01',
                'id_estatus_sat' => 1,
                'datos_extra' => json_encode([
                    'sector' => 'Tecnología',
                    'empleados' => 50
                ]),
                'email_facturacion_id' => $emailAdmin->id,
                'habilitado' => true,
            ]);

            $regimenPrincipal = DatosFiscalRegimenFiscal::create([
                'id_dato_fiscal' => $datosFiscalesPrincipal->id,
                'id_regimen' => 1,
                'fecha_inicio_regimen' => '2012-01-01',
                'predeterminado' => true,
            ]);

            DatosFiscalRegimenUsoCfdi::create([
                'id_dato_fiscal_regimen' => $regimenPrincipal->id,
                'uso_cfdi' => 'G03',
                'predeterminado' => true,
            ]);

            Direccion::create([
                'id_fiscal' => $datosFiscalesPrincipal->id,
                'id_tipo_direccion' => 1,
                'calle' => 'Av. Reforma',
                'num_exterior' => '123',
                'num_interior' => 'Piso 4',
                'colonia' => 'Juárez',
                'codigo_postal' => '06600',
                'municipio' => 'Cuauhtémoc',
                'estado' => 'Ciudad de México',
                'pais' => 'México'
            ]);

            Direccion::create([
                'id_fiscal' => $datosFiscalesPersonal->id,
                'id_tipo_direccion' => 2,
                'calle' => 'Calle Roma',
                'num_exterior' => '456',
                'colonia' => 'Condesa',
                'codigo_postal' => '06140',
                'municipio' => 'Cuauhtémoc',
                'estado' => 'Ciudad de México',
                'pais' => 'México'
            ]);

            $user->update([
                'datos_fiscales_principal' => $datosFiscalesPrincipal->id,
                'datos_fiscales_personal' => $datosFiscalesPersonal->id,
            ]);

            // ============================
            // ADMIN MESA DE AYUDA (ROL 4)
            // ============================

            $userMesaAyuda = User::create([
                'idRol' => 4, // Asumiendo que el rol 5 es para mesa de ayuda
                'password' => bcrypt('P@ssword1'),
                'intentos' => 0,
                'login_activo' => false, // Activo para que pueda iniciar sesión
                'id_mail_principal' => null,
            ]);

            $emailMesaAyuda = UserEmail::create([
                'user_id' => $userMesaAyuda->id,
                'email' => 'mesa.ayuda@hopewellsystem.com',
                'verificado' => true,
            ]);

            $phoneMesaAyuda = UserPhone::create([
                'user_id' => $userMesaAyuda->id,
                'telefono' => '5512345000',
                'verificado' => true,
            ]);

            $userMesaAyuda->update([
                'id_mail_principal' => $emailMesaAyuda->id,
                'id_telefono_principal' => $phoneMesaAyuda->id,
            ]);

            $datosFiscalesMesaAyuda = DatosFiscal::create([
                'id_usuario' => $userMesaAyuda->id,
                'nombre_razon' => 'Ana García',
                'primer_apellido' => 'García',
                'segundo_apellido' => 'Martínez',
                'es_persona_moral' => false,
                'rfc' => 'GAMA800515XYZ',
                'curp' => 'GAMA800515MDFRRN04',
                'lugar_emision' => 'Ciudad de México',
                'fecha_emision' => '2023-01-01',
                'fecha_inicio_op' => '2023-01-01',
                'id_estatus_sat' => 1,
                'email_facturacion_id' => $emailMesaAyuda->id,
                'habilitado' => true,
            ]);

            Direccion::create([
                'id_fiscal' => $datosFiscalesMesaAyuda->id,
                'id_tipo_direccion' => 2,
                'calle' => 'Av. Reforma',
                'num_exterior' => '500',
                'colonia' => 'Juárez',
                'codigo_postal' => '06600',
                'municipio' => 'Cuauhtémoc',
                'estado' => 'Ciudad de México',
                'pais' => 'México'
            ]);

            $userMesaAyuda->update([
                'datos_fiscales_principal' => $datosFiscalesMesaAyuda->id,
                'datos_fiscales_personal' => $datosFiscalesMesaAyuda->id,
            ]);

            $userMesaAyuda->save();

            // ============================
            // USUARIO EMPLEADO (ROL 5)
            // ============================

            $userEmpleado = User::create([
                'idRol' => 5,
                'password' => bcrypt('P@ssword1'),
                'intentos' => 0,
                'login_activo' => false,
                'id_mail_principal' => null,
            ]);

            $emailEmpleado = UserEmail::create([
                'user_id' => $userEmpleado->id,
                'email' => 'empleado@hopewellsystem.com',
                'verificado' => true,
            ]);

            $phoneEmpleado = UserPhone::create([
                'user_id' => $userEmpleado->id,
                'telefono' => '5512345678',
                'verificado' => true,
            ]);

            $userEmpleado->update([
                'id_mail_principal' => $emailEmpleado->id,
                'id_telefono_principal' => $phoneEmpleado->id,
            ]);

            $datosFiscalesEmpleado = DatosFiscal::create([
                'id_usuario' => $userEmpleado->id,
                'nombre_razon' => 'Carlos Hernández',
                'primer_apellido' => 'Hernández',
                'segundo_apellido' => 'López',
                'es_persona_moral' => false,
                'rfc' => 'HELC900101XYZ',
                'curp' => 'HELC900101HDFRPR08',
                'lugar_emision' => 'Guadalajara',
                'fecha_emision' => '2022-01-01',
                'fecha_inicio_op' => '2022-01-01',
                'id_estatus_sat' => 1,
                'email_facturacion_id' => $emailEmpleado->id,
                'habilitado' => true,
            ]);

            Direccion::create([
                'id_fiscal' => $datosFiscalesEmpleado->id,
                'id_tipo_direccion' => 2,
                'calle' => 'Av. Vallarta',
                'num_exterior' => '200',
                'colonia' => 'Arcos Vallarta',
                'codigo_postal' => '44130',
                'municipio' => 'Guadalajara',
                'estado' => 'Jalisco',
                'pais' => 'México'
            ]);

            $userEmpleado->update([
                'datos_fiscales_principal' => $datosFiscalesEmpleado->id,
                'datos_fiscales_personal' => $datosFiscalesEmpleado->id,
            ]);

            $userEmpleado->save();
        });
    }
}
