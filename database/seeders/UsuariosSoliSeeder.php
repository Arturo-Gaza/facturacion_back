<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SistemaTickets\CatDepartamentos;
use Illuminate\Support\Facades\DB;

class UsuariosSoliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::transaction(function () {

            $usuarios = [
                // [email, nombre, apellidoP, apellidoM, departamento]
                ['roman.trejo@alltub.com.mx', 'Roman', 'Trejo', 'Hernandez', 'Almacén de Materia Prima'],
                ['basilio.delacruz@alltub.com.mx', 'Basilio', 'de la Cruz', 'Miguel', 'Cadena de Suministros'],
                ['control.piso@alltub.com.mx', 'Control', 'de', 'Piso', 'Cadena de Suministros'],
                ['david.alfaro@alltub.com.mx', 'Francisco David', 'Alfaro', 'Centeno', 'Cadena de Suministros'],
                ['genesis.marmolejo@alltub.com.mx', 'Génesis Alicia', 'Marmolejo', 'Hernández', 'Cadena de Suministros'],
                ['humberto.sanchez@alltub.com.mx', 'Humberto', 'Sanchez', 'Sanchez', 'Cadena de Suministros'],
                ['jhoana.guzman@alltub.com.mx', 'Jhoana Yazbeth', 'Guzmán', 'Silva', 'Cadena de Suministros'],
                ['materia.prima@alltub.com.mx', 'Materia', 'Prima', '', 'Cadena de Suministros'],
                ['monserrat.abrego@alltub.com.mx', 'Monserrat', 'Abrego', 'Hernández', 'Cadena de Suministros'],
                ['ulises.balcazar@alltub.com.mx', 'Ulises Quetzalcoatl', 'Balcazar', 'Rodas', 'Cadena de Suministros'],
                ['marcela.vargas@alltub.com.mx', 'Marcela', 'Vargas', '', 'Cadena de Suministros'],
                ['ana.ayala@alltub.com.mx', 'Ana Maria', 'Ayala', 'Yebra', 'Calidad'],
                ['brenda.zapatero@alltub.com.mx', 'Brenda', 'Zapatero', 'Hernandez', 'Calidad'],
                ['eduardo.perez@alltub.com.mx', 'Eduardo', 'Pérez', 'Gonzalez', 'Calidad'],
                ['evelin.aviles@alltub.com.mx', 'Evelin', 'Aviles', 'Ventura', 'Calidad'],
                ['gustavo.flores@alltub.com.mx', 'Gustavo', 'Flores', 'Nieto', 'Calidad'],
                ['jessica.garcia@alltub.com.mx', 'Jessica', 'Garcia', 'Castro', 'Calidad'],
                ['miguel.blancas@alltub.com.mx', 'Miguel Abdiel', 'Blancas', 'Sanchez', 'Calidad'],
                ['mireya.vazquez@alltub.com.mx', 'Mireya', 'Vazquez', 'Alcantar', 'Calidad'],
                ['supervisor.calidad@alltub.com.mx', 'Supervisor', 'Calidad', '', 'Calidad'],
                ['pedro.herrera@alltub.com.mx', 'Pedro', 'Herrera', '', 'Dirección'],
                ['esteban.serrano@alltub.com.mx', 'Esteban', 'Serrano', 'Ponce', 'Dirección General'],
                ['rosario.gonzalez@alltub.com.mx', 'Maria del Rosario', 'Gonzalez', 'Dominguez', 'Dirección General'],
                ['brenda.gomez@alltub.com.mx', 'Brenda', 'Gomez', 'Medel', 'Finanzas'],
                ['eduardo.claudio@alltub.com.mx', 'Eduardo', 'Claudio', 'de la Cruz', 'Finanzas'],
                ['esmeralda.grande@alltub.com.mx', 'Esmeralda', 'Grande', 'Gonzalez', 'Finanzas'],
                ['faustino.montano@alltub.com.mx', 'Faustino', 'Montaño', 'Pastor', 'Finanzas'],
                ['francisco.hernandez@alltub.com.mx', 'Francisco', 'Hernández', 'Arágon', 'Finanzas'],
                ['karla.gonzalez@alltub.com.mx', 'Karla Lizeth', 'Gonzalez', 'Serna', 'Finanzas'],
                ['moises.herrera@alltub.com.mx', 'Moises Aaron', 'Herrera', 'del Toro', 'Ingeniería'],
                ['julio.hernandez@alltub.com.mx', 'Julio Cesar', 'Hernández', 'Hernández', 'Inspección de Procesos'],
                ['jorge.cisneros@alltub.com.mx', 'Jorge Alberto', 'Cisneros', 'Mosqueda', 'IT'],
                ['jose.alvarez@alltub.com.mx', 'Jose Luis', 'Alvarez', 'Santiago', 'IT'],
                ['miguel.betancourt@alltub.com.mx', 'Miguel Angel', 'Betancourt', 'Roman', 'Logística'],
                ['adrian.blancas@alltub.com.mx', 'Adrian', 'Blancas', 'Parrilla', 'Mantenimiento'],
                ['cesar.garcia@alltub.com.mx', 'Cesar Alberto', 'Garcia', 'Cruz', 'Mantenimiento'],
                ['christopher.jimenez@alltub.com.mx', 'Christopher Jorge', 'Jimenez', 'Badillo', 'Mantenimiento'],
                ['eduardo.pacheco@alltub.com.mx', 'Eduardo', 'Pacheco', 'Ortega', 'Mantenimiento'],
                ['mantenimiento@alltub.com.mx', 'Mantenimiento', 'Mantenimiento', '', 'Mantenimiento'],
                ['ramon.moreno@alltub.com.mx', 'Ramon', 'Moreno', 'Peralta', 'Mantenimiento'],
                ['ricardo.ordonez@alltub.com.mx', 'Ricardo Jair', 'Ordoñez', 'Alcántara', 'Mantenimiento'],
                ['alejandro.flores@alltub.com.mx', 'Alejandro', 'Flores', 'Badillo', 'Producción'],
                ['alejandro.vazquez@alltub.com.mx', 'Alejandro', 'Vazquez', 'Gutierrez', 'Producción'],
                ['alfredo.benitez@alltub.com.mx', 'Alfredo', 'Benitez', 'de los Santos', 'Producción'],
                ['cesar.mollinedo@alltub.com.mx', 'Cesar Ivan', 'Mollinedo', 'Javier', 'Producción'],
                ['fernando.martinez@alltub.com.mx', 'Fernando', 'Martinez', 'Obregon', 'Producción'],
                ['hiram.cruz@alltub.com.mx', 'Hiram', 'Cruz', 'Gonzalez', 'Producción'],
                ['ivan.garcia@alltub.com.mx', 'Ivan', 'Garcia', 'Flores', 'Producción'],
                ['jesus.moran@alltub.com.mx', 'Jesus', 'Morán', 'Barrera', 'Producción'],
                ['jorge.fragoso@alltub.com.mx', 'Jorge Alberto', 'Fragoso', 'Silva', 'Producción'],
                ['jose.espinosa@alltub.com.mx', 'Jose Juan', 'Espinosa', 'Albarran', 'Producción'],
                ['jose.hernandez@alltub.com.mx', 'Jose Manuel', 'Hernandez', 'Hernandez', 'Producción'],
                ['karina.rivero@alltub.com.mx', 'Karina', 'Rivero', 'Granillo', 'Producción'],
                ['marco.anaya@alltub.com.mx', 'Marco Antonio', 'Anaya', 'Benitez', 'Producción'],
                ['miguel.hernandez@alltub.com.mx', 'Miguel Angel', 'hernandez', 'Enriquez', 'Producción'],
                ['sandra.lugo@alltub.com.mx', 'Sandra Lizbeth', 'Lugo', 'Noguez', 'Producción'],
                ['tintas@alltub.com.mx', 'Tintas', 'Tintas', '', 'Producción'],
                ['yarimeth.aguilar@alltub.com.mx', 'Yarimeth', 'Aguilar', '', 'Producción'],
                ['brenda.garcia@alltub.com.mx', 'Brenda Arely', 'Garcia', 'Arellano', 'Recursos Humanos'],
                ['issel.diaz@alltub.com.mx', 'Issel Rebeca', 'Diaz', 'Salas', 'Recursos Humanos'],
                ['jorge.lara@alltub.com.mx', 'Jorge Alberto', 'Lara', 'Villegas', 'Recursos Humanos'],
                ['karen.plata@alltub.com.mx', 'Karen', 'Plata', 'Alanis', 'Recursos Humanos'],
                ['rodrigo.cortes@alltub.com.mx', 'Rodrigo Javier', 'Cortes', 'Rico', 'Recursos Humanos'],
                ['servicio.medico@alltub.com.mx', 'Servicio', 'Médico', '', 'Recursos Humanos'],
                ['thelma.cardenas@alltub.com.mx', 'Thelma Malinali', 'Cardenas', 'Martinez', 'Recursos Humanos'],
                ['vigilancia@alltub.com.mx', 'Vigilancia', 'Puerta', '2', 'Recursos Humanos'],
                ['hugo.ginera@alltub.com.mx', 'Hugo Rene', 'Ginera', 'Jauregui', 'Recursos Humanos'],
                ['isabel.gomez@alltub.com.mx', 'Aidee Isabel', 'Gómez', 'Gutierrez', 'Ventas'],
                ['jose.villalobos@alltub.com.mx', 'Jose Ignacio', 'Villalobos', 'Bonnabe', 'Ventas'],
                ['laura.lopez@alltub.com.mx', 'Laura', 'Lopez', 'De Leon', 'Ventas'],
            ];

            foreach ($usuarios as [$email, $nombre, $apP, $apM, $depto]) {


$departamento = CatDepartamentos::firstOrCreate(['descripcion' => $depto]);
                User::firstOrCreate(
                    ['email' => $email],
                    [
                        'user' => explode('@', $email)[0],
                        'name' => $nombre,
                        'apellidoP' => $apP,
                        'apellidoM' => $apM,
                        'idRol' => 4, // Usuario General/Solicitante
                        'id_departamento' => $departamento->id,
                        'password' => bcrypt('P@ssword1'),
                        'habilitado' => true,
                        'intentos' => 0,
                        'login_activo' => false,
                    ]
                );
            }
        });

    }
}
