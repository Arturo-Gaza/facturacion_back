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
            // Primero crear el usuario
            $user = User::create([
                'idRol' => 1,
                'password' => '$2y$12$bHqfPcMYy3GLmbxM5iF54eOTGofmXHHyzuSYgP4MCS1EWUF2wNQX6',
                'intentos' => 0,
                'login_activo' => false,
                'id_mail_principal' => null,
            ]);

            // Luego crear el email con el user_id correcto
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

            // Finalmente actualizar el usuario con el id_mail_principal
            $user->update([
                'id_mail_principal' => $emailAdmin->id,
                'id_telefono_principal' => $phoneAdmin->id,
            ]);
            $datosFiscalesPersonal = DatosFiscal::create([
                'id_usuario' => $user->id,
                'nombre_razon' => 'Administrador',
                'primer_apellido' => 'Pérez',
                'segundo_apellido' => 'Gómez',
                'nombre_comercial' => null,
                'es_persona_moral' => false, // Persona física
                'rfc' => 'PEGJ800101ABC', // RFC de persona física
                'curp' => 'PEGJ800101HDFRMN09',
                'idCIF' => null,
                'lugar_emision' => 'Ciudad de México',
                'fecha_emision' => '2020-01-01',
                'fecha_inicio_op' => '2020-01-01',
                'id_estatus_sat' => 1, // Asumiendo que 1 es estatus activo en SAT
                'datos_extra' => null,
                'email_facturacion_id' => $emailAdmin->id,
                'habilitado' => true,
            ]);

            // 6. Crear Datos Fiscales PRINCIPALES (para facturación)
            $datosFiscalesPrincipal = DatosFiscal::create([
                'id_usuario' => $user->id,
                'nombre_razon' => 'HopeWell Systems S.A. de C.V.',
                'primer_apellido' => null,
                'segundo_apellido' => null,
                'nombre_comercial' => 'HopeWell Systems',
                'es_persona_moral' => true, // Persona moral
                'rfc' => 'HWS120101ABC', // RFC de empresa
                'curp' => null,
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

            // 7. Agregar regímenes fiscales a los datos fiscales principales
            $regimenPrincipal = DatosFiscalRegimenFiscal::create([
                'id_dato_fiscal' => $datosFiscalesPrincipal->id,
                'id_regimen' => 1, // General de Ley Personas Morales
                'fecha_inicio_regimen' => '2012-01-01',
                'predeterminado' => true,
            ]);

            // 8. Agregar usos CFDI al régimen principal
            DatosFiscalRegimenUsoCfdi::create([
                'id_dato_fiscal_regimen' => $regimenPrincipal->id,
                'uso_cfdi' => 'G03', // Gastos en general
                'predeterminado' => true,
            ]);
            Direccion::create([
                'id_fiscal' => $datosFiscalesPrincipal->id,
                'id_tipo_direccion' => 1, // Domicilio fiscal
                'calle' => 'Av. Reforma',
                'num_exterior' => '123',
                'num_interior' => 'Piso 4',
                'colonia' => 'Juárez',
                'codigo_postal' => '06600',
                'municipio' => 'Cuauhtémoc',
                'estado' => 'Ciudad de México',
                'pais' => 'México'
            ]);

            // Dirección personal para datos fiscales personales
            Direccion::create([
                'id_fiscal' => $datosFiscalesPersonal->id,
                'id_tipo_direccion' => 2, // Domicilio personal
                'calle' => 'Calle Roma',
                'num_exterior' => '456',
                'colonia' => 'Condesa',
                'codigo_postal' => '06140',
                'municipio' => 'Cuauhtémoc',
                'estado' => 'Ciudad de México',
                'pais' => 'México'
            ]);

            // 12. Finalmente actualizar el usuario con las referencias a datos fiscales
            $user->update([
                'datos_fiscales_principal' => $datosFiscalesPrincipal->id,
                'datos_fiscales_personal' => $datosFiscalesPersonal->id,
            ]);
            $user->save();
        });
    }
}
