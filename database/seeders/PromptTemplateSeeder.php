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

TEXTO:
{\$textoOCR}

Estructura requerida:
{
    "establecimiento": "nombre del establecimiento o empresa",
    "monto": "monto total numérico (sin símbolos de moneda)",
    "fecha": "fecha de la transacción si está disponible",
    "productos": ["lista de productos o servicios identificados"],
    "moneda": "tipo de moneda (MXN, USD, etc.)",
    "direccion": "dirección del establecimiento si está disponible",
    "telefono": "teléfono del establecimiento si está disponible"
}

INSTRUCCIONES ESPECÍFICAS:
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