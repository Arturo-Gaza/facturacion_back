<?php

namespace Database\Seeders;

use App\Models\PromptTemplate;
use Illuminate\Database\Seeder;

class PromptTemplateSeeder extends Seeder
{
    public function run()
    {
        $prompts = [
            [
                'name' => 'Extracción de datos de tickets y facturas',
                'type' => 'receipt_extraction',
                'prompt' => <<<PROMPT
Del siguiente texto extraído de un ticket o factura, extrae la siguiente información en formato JSON:

DATOS:
{\$id}
{\$fecha}

TEXTO:
{\$textoOCR}

Estructura requerida:
{
    "num_ticket":numero o id del ticket,
    "establecimiento": "nombre del establecimiento o empresa",
    "monto": "monto total numérico (sin símbolos de moneda)",
    "fecha": "fecha de la transacción si está disponible",
    "productos": ["lista de productos o servicios identificados"],
    "moneda": "tipo de moneda (MXN, USD, etc.)",
    "direccion": "dirección del establecimiento si está disponible",
    "telefono": "teléfono del establecimiento si está disponible"
}

INSTRUCCIONES ESPECÍFICAS:
- Para num_ticket: buscar códigos que empiecen con T, F, #, o que contengan "ticket", "folio", "número", "ID", "transacción", "transaccion" o en general un codigo que parezca unico, respeta todas las letras y numeros que esten en el codigo
- Solo si no encuentras algun codigo que cumpla con la regla anterior genera uno con el formato T\$fecha\$id (con el formato de la fecha YYYYMMDD y con el id en 4 cifras rellenando los faltantes con ceros, pero solo si no encuentras uno, es importante que solo sea la ultima opción
- Para el monto: buscar números con decimales, usualmente el más grande es el total
- Para la fecha: buscar patrones de fecha (dd/mm/aaaa, mm/dd/aaaa, etc.)
- Para establecimiento: buscar nombres comerciales en las primeras líneas
- Para productos: identificar líneas que contengan cantidades, descripciones y precios
- Si algún dato no está presente, usar null

Devuelve ÚNICAMENTE el JSON válido, sin texto adicional.
PROMPT,
                'description' => 'Prompt para extraer datos estructurados de tickets y facturas mediante OCR',
            ],
            [
                'name' => 'Extracción de datos de facturas formales',
                'type' => 'invoice_extraction',
                'prompt' => <<<PROMPT
Del siguiente texto extraído de una factura formal, extrae la siguiente información en formato JSON:

TEXTO:
{\$textoOCR}

Estructura requerida:
{
    "emisor": "nombre o razón social del emisor",
    "receptor": "nombre o razón social del receptor",
    "fecha_emision": "fecha de emisión",
    "folio": "folio o número de factura",
    "subtotal": "subtotal numérico",
    "iva": "IVA numérico",
    "total": "total numérico",
    "rfc_emisor": "RFC del emisor si está presente",
    "rfc_receptor": "RFC del receptor si está presente",
    "moneda": "tipo de moneda (MXN, USD, etc.)",
    "forma_pago": "forma de pago si está especificada"
}

INSTRUCCIONES ESPECÍFICAS:
- Buscar secciones claramente identificadas como "Emisor", "Receptor", "Folio", etc.
- Identificar valores numéricos con sus etiquetas correspondientes
- Para RFCs: buscar patrones de 12-13 caracteres alfanuméricos
- Las fechas suelen estar en formatos específicos de facturación

Devuelve ÚNICAMENTE el JSON válido.
PROMPT,
                'description' => 'Prompt para extraer datos de facturas formales con RFC, subtotal, IVA, etc.',
            ],
            [
                'name' => 'Extracción simplificada para tickets pequeños',
                'type' => 'simple_receipt_extraction',
                'prompt' => <<<PROMPT
Del siguiente texto de un ticket pequeño, extrae SOLO los datos esenciales en JSON:

TEXTO:
{\$textoOCR}

Estructura requerida:
{
    "establecimiento": "nombre del establecimiento",
    "monto": "monto total",
    "fecha": "fecha si está visible"
}

INSTRUCCIONES:
- Enfócate solo en estos 3 campos
- Si no encuentras alguno, usa null
- Mantén la respuesta mínima y directa

SOLO el JSON, nada más.
PROMPT,
                'description' => 'Prompt simplificado para tickets pequeños con menos datos',
            ],
            [
                'name' => 'Validación y limpieza de texto OCR',
                'type' => 'text_validation',
                'prompt' => <<<PROMPT
Analiza el siguiente texto extraído por OCR y devuelve un JSON con evaluación de calidad:

TEXTO:
{\$textoOCR}

Estructura requerida:
{
    "calidad_texto": "buena|regular|mala",
    "confianza": 0-100,
    "errores_detectados": ["lista de posibles errores de OCR"],
    "recomendaciones": ["sugerencias para mejorar la extracción"],
    "es_factura": true/false,
    "es_ticket": true/false
}

Evalúa la legibilidad y calidad del texto OCR.
PROMPT,
                'description' => 'Prompt para evaluar la calidad del texto extraído por OCR',
            ],
            [
                'name' => 'Extracción de datos de Constancia de Situación Fiscal',
                'type' => 'cfdi_extraction',
                'prompt' => <<<PROMPT
Del siguiente texto extraído de una Constancia de Situación Fiscal (CFDI) del SAT, extrae la siguiente información en formato JSON:

TEXTO:
{\$textoOCR}

CATALGOGOS
regimenesFiscales
{\$regimenesFiscales}
estatusSat
{\$estatusSat}

REGLAS
-Todas las fechas damelas en formato "YYYY-MM-DD"

Estructura requerida:
{
    "id":"null",
    "id_usuario":"null",
    "rfc": "RFC del contribuyente",
    "curp": "CURP del contribuyente",
    "idCIF": "ID o folio de la constancia CFDI",
    "nombre_razon": "Nombre(s) del contribuyente",
    "primer_apellido": "Primer apellido del contribuyente",
    "segundo_apellido": "Segundo apellido del contribuyente",
    "nombre_completo": "Nombre completo del contribuyente",
    "fecha_inicio_op": "Fecha de inicio de operaciones",
    "fecha_ult_cambio_op":"Fecha cuando cambio de operacion si es que lo hizo",
    "id_estatus_sat": "Id del estatus en el padrón basandote en el catalogo 'estatusSat'",
    "fecha_ultimo_cambio": "Fecha del último cambio de estado",
    "es_persona_moral":"Si es persona moral true si es fisica false",
    "lugar_emision":"Lugar donde fue emitida la constancia",
    "fecha_emision":"Fecha cuando fue emitida la constancia",
    "nombre_comercial":"Nombre de la empresa solo en caso de ser una persona moral"
    "domicilioFiscal": {
        "codigo_postal": "Código Postal",
        "colonia": "Colonia",
        "estado": "Entidad federativa",
        "localidad": "Localidad",
        "municipio": "Municipio o demarcación territorial",
        "calle": "Nombre de la vialidad",
        "num_exterior": "Número exterior",
        "num_interior": "Número interior",
        "pais": "País (default: México)"
    },
    "regimenesFiscales": ["
    lista de los id de los regímenes fiscales basandote en el catalogo regimenesFiscales, si no encuentas coincidencia o no hay mandalo vaciode la siguiente manera;"
    "id_regimen":id del regimen en formato numerico,
    "predeterminado":"true en caso de haber un solo regimen",
    "fecha_inicio_regimen","Fecha Inicio del regimen",
    "usosCfdi":["un array vacio"]
    ]
    "email": "Correo electrónico",
    "telefono": "Número de teléfono",
    "fecha_emision": "Fecha de emisión de la constancia",
    "lugar_emision": "Lugar de emisión de la constancia",
    "datos_extra":"Solo si encuentras una seccion datos extra ponla",
    "email_facturacion_id":"null",
    "email_facturacion_id":"null",
    "habilitado":"true",
    "predeterminado":"false"

}

INSTRUCCIONES ESPECÍFICAS:
- Para RFC: buscar patrón de 12-13 caracteres alfanuméricos (ej: BUCO941019955)
- Para CURP: buscar patrón de 18 caracteres alfanuméricos (ej: BUCO941019HDFSMS00)
- El nombre completo suele estar después de "Nombre(s):", "PrimerApellido:" y "SegundoApellido:"
- Las fechas: buscar en formatos como "15 DE MARZO DE 2019" o "15/03/2019"
- Para regímenes: buscar después de "Regímenes:" o "Régimen"
- El estatus usualmente es "ACTIVO" o "BAJA"
- La fecha de emisión suele estar en "Lugar y Fecha de Emisión"

BUSCAR ESPECÍFICAMENTE ESTAS ETIQUETAS COMUNES:
- RFC:, CURP:, Nombre(s):, PrimerApellido:, SegundoApellido:
- Fecha inicio de operaciones:, Estatus en el padrón:
- Código Postal:, Nombre de la Colonia:, Nombre del Municipio:
- Nombre de la Entidad Federativa:, Correo Electrónico:
- Regímenes:, Fecha de Emisión:

Si algún dato no está presente, usar null. Devuelve ÚNICAMENTE el JSON válido.
PROMPT,
                'description' => 'Prompt para extraer datos de Constancia de Situación Fiscal del SAT',
            ],

            [
                'name' => 'Limpieza y corrección de texto de CFDI',
                'type' => 'cfdi_cleaning',
                'prompt' => <<<PROMPT
Analiza el siguiente texto extraído de una Constancia de Situación Fiscal y corrígelo mejorando los espacios y formato:

TEXTO ORIGINAL:
{\$textoOCR}

INSTRUCCIONES DE CORRECCIÓN:
1. Corrige los espacios faltantes entre palabras (ej: "OSCARALFREDO" → "OSCAR ALFREDO")
2. Separa correctamente los campos unidos (ej: "Fechainiciodeoperaciones" → "Fecha inicio de operaciones")
3. Mantén la estructura de etiquetas como "RFC:", "CURP:", etc.
4. Conserva todos los datos originales, solo mejora el formato
5. No inventes información, solo corrige el formato

Devuelve ÚNICAMENTE el texto corregido, sin comentarios adicionales.
PROMPT,
                'description' => 'Prompt para limpiar y corregir formato de texto de CFDI',
            ],

            [
                'name' => 'Validación de Constancia de Situación Fiscal',
                'type' => 'cfdi_validation',
                'prompt' => <<<PROMPT
Valida la siguiente Constancia de Situación Fiscal y devuelve un JSON con el análisis:

TEXTO:
{\$textoOCR}

{
    "id": null,
    "id_usuario": null,
    "idCIF": "valor_extraído_o_null",
    "lugar_emision": "valor_extraído_o_null",
    "fecha_emision": "YYYY-MM-DD",
    "nombre_razon": "valor_extraído_o_null",
    "nombre_comercial": "valor_extraído_o_null",
    "es_persona_moral": true/false,
    "rfc": "valor_extraído_o_null",
    "curp": "valor_extraído_o_null",
    "fecha_inicio_op": "YYYY-MM-DD",
    "fecha_ult_cambio_op": "YYYY-MM-DD",
    "id_estatus_sat": 1, 
    "datos_extra": null,
    "email_facturacion_id": null,
    "primer_apellido": "valor_extraído_o_null",
    "segundo_apellido": "valor_extraído_o_null",
    "predeterminado": false,
    "domicilioFiscal": {
        "id_direccion": null,
        "id_fiscal": null,
        "id_tipo_direccion": 1,
        "calle": "valor_extraído_o_null",
        "num_exterior": "valor_extraído_o_null",
        "num_interior": "valor_extraído_o_null",
        "colonia": "valor_extraído_o_null",
        "localidad": "valor_extraído_o_null",
        "municipio": "valor_extraído_o_null",
        "estado": "valor_extraído_o_null",
        "codigo_postal": "valor_extraído_o_null",
        "pais": "México"
    },
    "regimenesFiscales": [
        {
            "id": null,
            "fecha_inicio_regimen": "YYYY-MM-DD",
            "id_dato_fiscal": null,
            "id_regimen": null,
            "predeterminado": true/false,
            "nombre_regimen": "valor_extraído_o_null",
            "usosCfdi": [
                {
                    "id": null,
                    "id_dato_fiscal_regimen": null,
                    "uso_cfdi": "valor_extraído_o_null",
                    "predeterminado": true/false
                }
            ]
        }
    ]
}

CRITERIOS DE VALIDACIÓN:
- RFC debe tener formato correcto (12-13 caracteres)
- CURP debe tener formato correcto (18 caracteres)  
- Debe incluir fecha de emisión reciente
- Debe tener estatus claro (ACTIVO/BAJA)
- Debe contener datos básicos completos (nombre, dirección, etc.)

Devuelve ÚNICAMENTE el JSON de validación.
PROMPT,
                'description' => 'Prompt para validar integridad de Constancia de Situación Fiscal',
            ]
        
        ];

        foreach ($prompts as $promptData) {
            PromptTemplate::updateOrCreate(
                ['type' => $promptData['type']],
                $promptData
            );
        }

        $this->command->info('Prompts de IA creados exitosamente!');
    }
}