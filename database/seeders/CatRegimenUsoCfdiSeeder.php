<?php

namespace Database\Seeders;

use App\Models\Catalogos\CatRegimenesFiscales;
use App\Models\CatUsoCfdi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatRegimenUsoCfdiSeeder extends Seeder
{
    public function run()
    {
        
        // 1. Insertar regímenes fiscales
        $regimenesData = [
            ['clave' => '601', 'descripcion' => 'General de Ley Personas Morales', 'aplica_persona_fisica' => false, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '603', 'descripcion' => 'Personas Morales con Fines no Lucrativos', 'aplica_persona_fisica' => false, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '605', 'descripcion' => 'Sueldos y Salarios e Ingresos Asimilados a Salarios', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '606', 'descripcion' => 'Arrendamiento', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '607', 'descripcion' => 'Régimen de Enajenación o Adquisición de Bienes', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '608', 'descripcion' => 'Demás ingresos', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '610', 'descripcion' => 'Residentes en el Extranjero sin Establecimiento Permanente en México', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '611', 'descripcion' => 'Ingresos por Dividendos (socios y accionistas)', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '612', 'descripcion' => 'Personas Físicas con Actividades Empresariales y Profesionales', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '614', 'descripcion' => 'Ingresos por intereses', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '615', 'descripcion' => 'Régimen de los ingresos por obtención de premios', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '616', 'descripcion' => 'Sin obligaciones fiscales', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '620', 'descripcion' => 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos', 'aplica_persona_fisica' => false, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '621', 'descripcion' => 'Incorporación Fiscal', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '622', 'descripcion' => 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras', 'aplica_persona_fisica' => false, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '623', 'descripcion' => 'Opcional para Grupos de Sociedades', 'aplica_persona_fisica' => false, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '624', 'descripcion' => 'Coordinados', 'aplica_persona_fisica' => false, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '625', 'descripcion' => 'Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['clave' => '626', 'descripcion' => 'Régimen Simplificado de Confianza', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
        ];

        foreach ($regimenesData as $regimen) {
            CatRegimenesFiscales::create($regimen);
        }

        // 2. Insertar usos CFDI
        $usosCfdiData = [
            ['usoCFDI' => 'G01', 'descripcion' => 'Adquisición de mercancías', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'G02', 'descripcion' => 'Devoluciones, descuentos o bonificaciones', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'G03', 'descripcion' => 'Gastos en general', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'I01', 'descripcion' => 'Construcciones', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'I02', 'descripcion' => 'Mobiliario y equipo de oficina por inversiones', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'I03', 'descripcion' => 'Equipo de transporte', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'I04', 'descripcion' => 'Equipo de computo y accesorios', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'I05', 'descripcion' => 'Dados, troqueles, moldes, matrices y herramental', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'I06', 'descripcion' => 'Comunicaciones telefónicas', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'I07', 'descripcion' => 'Comunicaciones satelitales', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'I08', 'descripcion' => 'Otra maquinaria y equipo', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'D01', 'descripcion' => 'Honorarios médicos, dentales y gastos hospitalarios', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'D02', 'descripcion' => 'Gastos médicos por incapacidad o discapacidad', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'D03', 'descripcion' => 'Gastos funerales', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'D04', 'descripcion' => 'Donativos', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'D05', 'descripcion' => 'Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'D06', 'descripcion' => 'Aportaciones voluntarias al SAR', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'D07', 'descripcion' => 'Primas por seguros de gastos médicos', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'D08', 'descripcion' => 'Gastos de transportación escolar obligatoria', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'D09', 'descripcion' => 'Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'D10', 'descripcion' => 'Pagos por servicios educativos (colegiaturas)', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'S01', 'descripcion' => 'Sin efectos fiscales', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'CP01', 'descripcion' => 'Pagos', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => true, 'fecha_inicio_vigencia' => '2022-01-01'],
            ['usoCFDI' => 'CN01', 'descripcion' => 'Nómina', 'aplica_persona_fisica' => true, 'aplica_persona_moral' => false, 'fecha_inicio_vigencia' => '2022-01-01'],
        ];

        foreach ($usosCfdiData as $uso) {
            CatUsoCfdi::create($uso);
        }

        // 3. Crear relaciones en la tabla pivot
        $relaciones = [
            'G01' => ['601', '603', '606', '612', '620', '621', '622', '623', '624', '625', '626'],
            'G02' => ['601', '603', '606', '612', '620', '621', '622', '623', '624', '625', '626'],
            'G03' => ['601', '603', '606', '612', '620', '621', '622', '623', '624', '625', '626'],
            'I01' => ['601', '603', '606', '612', '620', '621', '622', '623', '624', '625', '626'],
            'I02' => ['601', '603', '606', '612', '620', '621', '622', '623', '624', '625', '626'],
            'I03' => ['601', '603', '606', '612', '620', '621', '622', '623', '624', '625', '626'],
            'I04' => ['601', '603', '606', '612', '620', '621', '622', '623', '624', '625', '626'],
            'I05' => ['601', '603', '606', '612', '620', '621', '622', '623', '624', '625', '626'],
            'I06' => ['601', '603', '606', '612', '620', '621', '622', '623', '624', '625', '626'],
            'I07' => ['601', '603', '606', '612', '620', '621', '622', '623', '624', '625', '626'],
            'I08' => ['601', '603', '606', '612', '620', '621', '622', '623', '624', '625', '626'],
            'D01' => ['605', '606', '608', '611', '612', '614', '607', '615', '625'],
            'D02' => ['605', '606', '608', '611', '612', '614', '607', '615', '625'],
            'D03' => ['605', '606', '608', '611', '612', '614', '607', '615', '625'],
            'D04' => ['605', '606', '608', '611', '612', '614', '607', '615', '625'],
            'D05' => ['605', '606', '608', '611', '612', '614', '607', '615', '625'],
            'D06' => ['605', '606', '608', '611', '612', '614', '607', '615', '625'],
            'D07' => ['605', '606', '608', '611', '612', '614', '607', '615', '625'],
            'D08' => ['605', '606', '608', '611', '612', '614', '607', '615', '625'],
            'D09' => ['605', '606', '608', '611', '612', '614', '607', '615', '625'],
            'D10' => ['605', '606', '608', '611', '612', '614', '607', '615', '625'],
            'S01' => ['601', '603', '605', '606', '608', '610', '611', '612', '614', '616', '620', '621', '622', '623', '624', '607', '615', '625', '626'],
            'CP01' => ['601', '603', '605', '606', '608', '610', '611', '612', '614', '616', '620', '621', '622', '623', '624', '607', '615', '625', '626'],
            'CN01' => ['605'],
        ];

        foreach ($relaciones as $usoCFDI => $clavesRegimen) {
            $usoModel = CatUsoCfdi::find($usoCFDI);
            
            if ($usoModel) {
                // Obtener los IDs de los regímenes basados en sus claves
                $regimenesIds = CatRegimenesFiscales::whereIn('clave', $clavesRegimen)
                    ->pluck('id_regimen')
                    ->toArray();
                
                // Insertar directamente en la tabla pivot
                foreach ($regimenesIds as $idRegimen) {
                    DB::table('regimen_uso_cfdi')->insert([
                        'id_regimen' => $idRegimen,
                        'usoCFDI' => $usoCFDI,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

 }
}