<?php

namespace Database\Seeders;

use App\Models\Catalogos\CatGpoFamilia;
use App\Models\SistemaTickets\CatMoneda;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class GpoFamiliaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ["clave" => "YBZ10", "desc1" => "Servicios Grls. Adm.", "desc2" => "Materiales Varios"],
            ["clave" => "SCRAP", "desc1" => "Scrap", "desc2" => "Materiales de Scrap"],
            ["clave" => "YBD01", "desc1" => "Grupo articulos D01", "desc2" => "Grupo material Servicios"],
            ["clave" => "YBF01", "desc1" => "Grupo PT", "desc2" => "Grupo material de Producto Terminado"],
            ["clave" => "YBF02", "desc1" => "Grupo articulos F02", "desc2" => "Grupo material Proceso Produccion"],
            ["clave" => "YBR01", "desc1" => "Cospel", "desc2" => "Grupo material aluminio"],
            ["clave" => "YBR02", "desc1" => "Tapas", "desc2" => "Grupo materiales tapas"],
            ["clave" => "YBR03", "desc1" => "Esmaltes", "desc2" => "Grupo material Esmaltes"],
            ["clave" => "YBR04", "desc1" => "Tintas", "desc2" => "Grupo material tintas"],
            ["clave" => "YBR05", "desc1" => "Embalaje", "desc2" => "Grupo material Embalaje"],
            ["clave" => "YBR06", "desc1" => "Recubrimientos Inter", "desc2" => "Grupo de Recubrimientos Internos"],
            ["clave" => "YBR07", "desc1" => "Sellador", "desc2" => "Grupo de materiales para Sellar"],
            ["clave" => "YBR08", "desc1" => "Quimicos", "desc2" => "Quimicos"],
            ["clave" => "YBR09", "desc1" => "Cajas", "desc2" => "Grupo material cajas"],
            ["clave" => "YBS01", "desc1" => "Tintas Elaboradas", "desc2" => "Tintas"],
            ["clave" => "YBS02", "desc1" => "Grupo articulos S02", "desc2" => "Grupo material Generico"],
            ["clave" => "YBSVM1", "desc1" => "material group yb100", "desc2" => "material group yb100"],
            ["clave" => "YBSVS1", "desc1" => "Scrap y Desperdicio", "desc2" => "Scrap y Desperdicio"],
            ["clave" => "YBT01", "desc1" => "Grupo articulos T01", "desc2" => "Grupo material Comercializables"],
            ["clave" => "YBU01", "desc1" => "Baleros", "desc2" => "Mantenimiento"],
            ["clave" => "YBU02", "desc1" => "Banda Doble Dentada", "desc2" => "Mantenimiento"],
            ["clave" => "YBU03", "desc1" => "Banda Dentada", "desc2" => "Mantenimiento"],
            ["clave" => "YBU04", "desc1" => "Banda Plana Sinfín", "desc2" => "Mantenimiento"],
            ["clave" => "YBU05", "desc1" => "Banda Tipo A", "desc2" => "Mantenimiento"],
            ["clave" => "YBU06", "desc1" => "Banda Tipo B", "desc2" => "Mantenimiento"],
            ["clave" => "YBU07", "desc1" => "Banda Tipo C", "desc2" => "Mantenimiento"],
            ["clave" => "YBU08", "desc1" => "Cadenas", "desc2" => "Mantenimiento"],
            ["clave" => "YBU09", "desc1" => "Candados", "desc2" => "Mantenimiento"],
            ["clave" => "YBU10", "desc1" => "Catarinas", "desc2" => "Mantenimiento"],
            ["clave" => "YBU11", "desc1" => "Electrico", "desc2" => "Mantenimiento"],
            ["clave" => "YBU12", "desc1" => "Embrague Neumatico", "desc2" => "Mantenimiento"],
            ["clave" => "YBU13", "desc1" => "Grasas y Lubricantes", "desc2" => "Mantenimiento"],
            ["clave" => "YBU14", "desc1" => "Guia", "desc2" => "Mantenimiento"],
            ["clave" => "YBU15", "desc1" => "Hidraulico", "desc2" => "Mantenimiento"],
            ["clave" => "YBU16", "desc1" => "Lainas", "desc2" => "Mantenimiento"],
            ["clave" => "YBU17", "desc1" => "Material p. Maquinar", "desc2" => "Mantenimiento"],
            ["clave" => "YBU18", "desc1" => "Neumatico", "desc2" => "Mantenimiento"],
            ["clave" => "YBU19", "desc1" => "Pasta para Embragues", "desc2" => "Mantenimiento"],
            ["clave" => "YBU20", "desc1" => "Reductor Velocidad", "desc2" => "Mantenimiento"],
            ["clave" => "YBU21", "desc1" => "Retenes", "desc2" => "Mantenimiento"],
            ["clave" => "YBU22", "desc1" => "Servomotores", "desc2" => "Mantenimiento"],
            ["clave" => "YBU23", "desc1" => "Tornillos", "desc2" => "Mantenimiento"],
            ["clave" => "YBX01", "desc1" => "Laca", "desc2" => "Utillaje"],
            ["clave" => "YBX02", "desc1" => "Tarraja", "desc2" => "Utillaje"],
            ["clave" => "YBX03", "desc1" => "Prensa", "desc2" => "Utillaje"],
            ["clave" => "YBX04", "desc1" => "Esmalte", "desc2" => "Utillaje"],
            ["clave" => "YBX05", "desc1" => "Litografia", "desc2" => "Utillaje"],
            ["clave" => "YBX06", "desc1" => "Tapadora", "desc2" => "Utillaje"],
            ["clave" => "YBX07", "desc1" => "Uso en general", "desc2" => "Utillaje"],
            ["clave" => "YBX08", "desc1" => "Latex", "desc2" => "Utillaje"],
            ["clave" => "YBX09", "desc1" => "Probadora de Fugas", "desc2" => "Utillaje"],
            ["clave" => "YBZ01", "desc1" => "Art. de Laboratorio", "desc2" => "Materiales Varios"],
            ["clave" => "YBZ02", "desc1" => "Art. de Limpieza", "desc2" => "Materiales Varios"],
            ["clave" => "YBZ03", "desc1" => "Art. Diversos", "desc2" => "Materiales Varios"],
            ["clave" => "YBZ04", "desc1" => "Art. Prom. e Impr.", "desc2" => "Materiales Varios"],
            ["clave" => "YBZ05", "desc1" => "Equipo Comp. y Cons.", "desc2" => "Materiales Varios"],
            ["clave" => "YBZ06", "desc1" => "Ferretería", "desc2" => "Materiales Varios"],
            ["clave" => "YBZ07", "desc1" => "Papelería y Art Escr", "desc2" => "Materiales Varios"],
            ["clave" => "YBZ08", "desc1" => "Seguridad Ind y MAM", "desc2" => "Materiales Varios"],
            ["clave" => "YBZ09", "desc1" => "Servicio Médico", "desc2" => "Materiales Varios"],

            ["clave" => "YBZ11", "desc1" => "Serv. Grls. Mantto.", "desc2" => "Materiales Varios"],
            ["clave" => "YBZ12", "desc1" => "Tramites Autoridad", "desc2" => "Materiales Varios"],
            ["clave" => "Z1000", "desc1" => "AF Terrenos", "desc2" => "AF Terrenos"],
            ["clave" => "Z1100", "desc1" => "AF Edificios", "desc2" => "AF Edificios"],
            ["clave" => "Z2000", "desc1" => "AF Maquinaria y Equi", "desc2" => "AF Maquinaria y Equipo"],
            ["clave" => "Z2500", "desc1" => "AF Desarrollos Inter", "desc2" => "AF Desarrollos Internos"],
            ["clave" => "Z2600", "desc1" => "AF Equipo Mantenimie", "desc2" => "AF Equipo Mantenimiento"],
            ["clave" => "Z2700", "desc1" => "AF Moldes y Troquele", "desc2" => "AF Moldes y Troqueles"],
            ["clave" => "Z2900", "desc1" => "AF Equipo de Computo", "desc2" => "AF Equipo de Computo"],
            ["clave" => "Z3000", "desc1" => "AF Equipo de Oficina", "desc2" => "AF Equipo de Oficina"],
            ["clave" => "Z3100", "desc1" => "AF Vehículos", "desc2" => "AF Vehículos"],
            ["clave" => "Z3200", "desc1" => "AF Licencias y Softw", "desc2" => "AF Licencias y Software"],
            ["clave" => "Z3300", "desc1" => "AF Gastos de Instala", "desc2" => "AF Gastos de Instalació"],
            ["clave" => "Z3400", "desc1" => "AF Act de poco Valor", "desc2" => "AF Act de poco Valor"],
            ["clave" => "Z4000", "desc1" => "AF AF en curso", "desc2" => "AF AF en curso"],
        ];

        foreach ($items as $item) {
            CatGpoFamilia::create([
                'clave_gpo_familia' => $item['clave'],
                'descripcion_gpo_familia' => $item['desc1'],
                'descripcion_gpo_familia_2' => $item['desc2'],
            ]);
        }
    }
}
