<?php

namespace App\Services;

use App\Models\PromptTemplate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIDataExtractionService
{
    private string $apiKey;
    private string $apiUrl;
    private string $provider;

    public function __construct(string $provider = null)
    {
        $this->provider = $provider ?: config('ocr.default_ai_provider');
        $config = config("ocr.providers.{$this->provider}");

        $this->apiKey = $config['api_key'];
        $this->apiUrl = $config['api_url'];
    }

    public function extractStructuredData(string $textoOCR, string $promptType = 'receipt_extraction',array $parameters=null): array
    {
        $prompt = $this->getPromptTemplate($promptType, $textoOCR);
        if ($parameters) {
            foreach ($parameters as $key => $value) {
                $placeholder = '{$' . $key . '}';
                $prompt = str_replace($placeholder, $value, $prompt);
            }
        }

        if ($this->provider === 'gemini') {
            return $this->extractWithGemini($prompt, $textoOCR);
        }

        return $this->fallbackExtraction($textoOCR);
    }
    public function extractStructuredDataPDF($file, string $promptType = 'receipt_extraction', array $parameters = null)
    {


        $promptTem = PromptTemplate::where('type', $promptType)->first();

        $prompt = str_replace('{$textoOCR}', "", $promptTem->prompt);
        if ($parameters) {
            foreach ($parameters as $key => $value) {
                $placeholder = '{$' . $key . '}';
                $prompt = str_replace($placeholder, $value, $prompt);
            }
        }
        // Leer y codificar en base64
        $pdfData = base64_encode(file_get_contents($file->getRealPath()));

        // Llamar a la API de Gemini
        $response = Http::timeout(60)->withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $this->apiKey, [
            'contents' => [[
                'parts' => [
                    [
                        'inline_data' => [
                            'mime_type' => 'application/pdf',
                            'data' => $pdfData,
                        ]
                    ],
                    [
                        'text' => $prompt
                    ]
                ]
            ]]
        ]);
        $json = $response->json();

        $summary = $json['candidates'][0]['content']['parts'][0]['text'] ?? 'No summary found';
        $cleanedJson = $this->extractJsonFromString($summary);
        return $cleanedJson;
    }

    private function extractJsonFromString(string $text)
    {
        // Intentar encontrar JSON dentro de bloques de código ```json ... ```
        if (preg_match('/```json\s*(.*?)\s*```/s', $text, $matches)) {
            $jsonString = $matches[1];
        } else {
            // Si no hay bloque de código, usar el texto completo
            $jsonString = $text;
        }

        // Limpiar el string
        $jsonString = trim($jsonString);

        // Intentar parsear como JSON
        $data = json_decode($jsonString, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }

        // Si falla, intentar encontrar cualquier objeto JSON en el texto
        if (preg_match('/\{.*\}/s', $jsonString, $matches)) {
            $data = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
        }

        return null;
    }

    private function extractWithGemini(string $prompt, string $textoOCR): array
    {
        try {
            $response = Http::timeout(60)
                ->retry(3, 1000)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-goog-api-key' => $this->apiKey
                ])
                ->post($this->apiUrl, [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'maxOutputTokens' => 1000,
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $jsonString = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                return $this->parseJSONResponse($jsonString);
            }
        } catch (\Exception $e) {
            Log::error("Error con AI API: " . $e->getMessage());
        }

        return $this->fallbackExtraction($textoOCR);
    }

    private function getPromptTemplate(string $type, string $textoOCR): string
    {
        // Primero intenta obtener de la base de datos
        $template = PromptTemplate::where('type', $type)->first();

        if ($template) {
            return str_replace('{$textoOCR}', $textoOCR, $template->prompt);
        }

        // Fallback a prompts predefinidos
        return $this->getDefaultPrompt($type, $textoOCR);
    }

    private function getDefaultPrompt(string $type, string $textoOCR): string
    {
        $prompts = [
            'receipt_extraction' => <<<PROMPT
Del siguiente texto extraído de un ticket o factura, extrae la siguiente información en formato JSON:

TEXTO:
{$textoOCR}

Estructura requerida:
{
    "establecimiento": "nombre del establecimiento o empresa",
    "monto": "monto total numérico (sin símbolos de moneda)",
    "fecha": "fecha de la transacción si está disponible",
    "productos": ["lista de productos o servicios identificados"],
    "moneda": "tipo de moneda (MXN, USD, etc.)"
}

Si algún dato no está presente, usa null. Devuelve ÚNICAMENTE el JSON.
PROMPT,

            'invoice_extraction' => <<<PROMPT
// Otro prompt para facturas...
PROMPT
        ];

        return $prompts[$type] ?? $prompts['receipt_extraction'];
    }

    private function parseJSONResponse(string $jsonString): array
    {
        $jsonString = preg_replace('/```json|```/', '', $jsonString);
        $jsonString = trim($jsonString);

        try {
            $datos = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);
            return is_array($datos) ? $datos : [];
        } catch (\JsonException $e) {
            Log::error("Error parseando JSON de IA: " . $e->getMessage());
            return [];
        }
    }

    private function fallbackExtraction(string $textoOCR): array
    {
        preg_match('/(\$|MXN\s*)(\d+\.?\d*)/', $textoOCR, $matchesMonto);
        $monto = $matchesMonto[2] ?? null;

        $lineas = explode("\n", $textoOCR);
        $establecimiento = trim($lineas[0] ?? '');

        return [
            'establecimiento' => $establecimiento ?: null,
            'monto' => $monto ? (float)$monto : null,
            'fecha' => null,
            'productos' => [],
            'moneda' => 'MXN'
        ];
    }
}
