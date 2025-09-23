<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\SolicitudRepositoryInterface;
use App\Models\Solicitud;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SolicitudRepository implements SolicitudRepositoryInterface
{

    private string $apiKey;
    private string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.google_vision.api_key', 'AIzaSyAYG5Ww5LMVZFVU9KoBbdwQAArpX6RX5k4');
        $this->apiUrl = 'https://vision.googleapis.com/v1/images:annotate';
    }

    public function getAll()
    {
        return Solicitud::with(['usuario', 'empleado', 'estadoSolicitud'])->get();
    }

    public function getByID($id): ?Solicitud
    {
        return Solicitud::with(['usuario', 'empleado', 'estadoSolicitud'])->find($id);
    }

    public function store(Request $request): Solicitud
    {
        $solicitud = new Solicitud();
        $solicitud->usuario_id = $request->usuario_id;
        $solicitud->estado_id = 1; // Estado por defecto

        // Guardar imagen
        if ($request->hasFile('imagen')) {
            $rutaImagen = $solicitud->guardarImagen($request->file('imagen'));
            $solicitud->imagen_url = $rutaImagen;
        }

        $solicitud->save();

        return $solicitud;
    }

    public function update(array $data, $id): ?Solicitud
    {
        $solicitud = Solicitud::find($id);
        if ($solicitud) {
            $solicitud->update($data);
        }
        return $solicitud;
    }

    public function getByUsuario(int $usuario_id)
    {
        return Solicitud::where('usuario_id', $usuario_id)
            ->with(['usuario', 'empleado', 'estadoSolicitud'])
            ->get();
    }

    public function obtenerImagen(int $id)
    {
        $solicitud = Solicitud::find($id);
        if (!$solicitud->imagen_url || !Storage::disk('public')->exists($solicitud->getRawOriginal('imagen_url'))) {
            Log::error("Imagen no encontrada para solicitud: {$solicitud->id}");
            return null;
        }

        // Obtener la imagen en base64
        $imagePath = $solicitud->getRutaImagenAttribute();
        $imageData = base64_encode(file_get_contents($imagePath));
        return $imageData;
    }

    public function procesar(int $id_sol)
    {
        $solicitud = Solicitud::find($id_sol);

        if (!$solicitud) {
            return null;
        }

        // Procesar imagen con Google Vision
        $textoOCR = $this->procesarImagenConGoogleVision($solicitud);



        if ($textoOCR) {
            // Extraer datos estructurados con IA
            $datosExtraidos = $this->extraerDatosConIA($textoOCR);

            $solicitud->update([
                'texto_ocr' => $textoOCR,
                'establecimiento' => $datosExtraidos['establecimiento'] ?? null,
                'monto' => $datosExtraidos['monto'] ?? null,
                'texto_json' => json_encode($datosExtraidos)
            ]);
        }


        return $solicitud->fresh();
    }



    public function getGeneralByUsuario(int $usuario_id)
    {
        // Obtener conteos con los nombres de estado desde el catálogo
        $conteos = Solicitud::where('solicitudes.usuario_id', $usuario_id)
            ->where('solicitudes.created_at', '>=', now()->subDays(30))
            ->join('cat_estatus_solicitud', 'solicitudes.estado_id', '=', 'cat_estatus_solicitud.id')
            ->selectRaw('cat_estatus_solicitud.descripcion_estatus_solicitud as estado, COUNT(*) as count')
            ->groupBy('cat_estatus_solicitud.descripcion_estatus_solicitud')
            ->pluck('count', 'estado');

        $total = $conteos->sum();

        $porcentaje = [];
        $absoluto = [];

        foreach ($conteos as $estado => $count) {
            $absoluto[$estado] = $count;
            $porcentaje[$estado] = $total > 0 ? round(($count / $total) * 100, 2) : 0;
        }

        return [
            'total' => $total,
            'porcentaje' => $porcentaje,
            'absoluto' => $absoluto
        ];
    }

    private function procesarImagenConGoogleVision(Solicitud $solicitud): ?string
    {
        try {
            // Verificar que existe la imagen
            if (!$solicitud->imagen_url || !Storage::disk('public')->exists($solicitud->getRawOriginal('imagen_url'))) {
                Log::error("Imagen no encontrada para solicitud: {$solicitud->id}");
                return null;
            }

            // Obtener la imagen en base64
            $imagePath = $solicitud->getRutaImagenAttribute();
            $imageData = base64_encode(file_get_contents($imagePath));

            // Preparar la solicitud a la API
            $requestBody = [
                'requests' => [
                    [
                        'image' => [
                            'content' => $imageData
                        ],
                        'features' => [
                            [
                                'type' => 'TEXT_DETECTION',
                                'maxResults' => 1
                            ]
                        ]
                    ]
                ]
            ];

            // Hacer la petición a la API
            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->post("{$this->apiUrl}?key={$this->apiKey}", $requestBody);

            if ($response->successful()) {
                $data = $response->json();

                // Extraer texto detectado
                return $this->extraerTextoOCR($data);
            } else {
                Log::error("Error en API Google Vision: " . $response->body(), [
                    'solicitud_id' => $solicitud->id,
                    'status' => $response->status()
                ]);
                return null;
            }
        } catch (RequestException $e) {
            Log::error("Error de conexión con Google Vision: " . $e->getMessage(), [
                'solicitud_id' => $solicitud->id
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("Error procesando imagen: " . $e->getMessage(), [
                'solicitud_id' => $solicitud->id
            ]);
            return null;
        }
    }

    private function extraerTextoOCR(array $apiResponse): ?string
    {
        if (!isset($apiResponse['responses'][0]['textAnnotations'][0]['description'])) {
            return null;
        }

        $texto = $apiResponse['responses'][0]['textAnnotations'][0]['description'];

        // Limpiar y formatear el texto
        return $this->limpiarTextoOCR($texto);
    }

    private function limpiarTextoOCR(string $texto): string
    {
        // Eliminar espacios múltiples y saltos de línea excesivos
        $texto = preg_replace('/\s+/', ' ', $texto);

        // Trim y limpiar
        return trim($texto);
    }

    /**
     * Procesar múltiples solicitudes
     */
    public function procesarMultiple(array $ids): array
    {
        $resultados = [];

        foreach ($ids as $id) {
            $solicitud = $this->procesar($id);
            $resultados[] = [
                'id' => $id,
                'procesado' => (bool) $solicitud,
                'texto_ocr' => $solicitud?->texto_ocr
            ];
        }

        return $resultados;
    }

    /**
     * Verificar conectividad con la API
     */
    public function verificarConexion(): bool
    {
        try {
            $response = Http::timeout(10)
                ->get("https://vision.googleapis.com/v1/images:annotate?key={$this->apiKey}");

            return $response->status() !== 401; // 401 sería clave inválida
        } catch (\Exception $e) {
            return false;
        }
    }

    private function extraerDatosConIA(string $textoOCR): array
    {
        try {
            $prompt = $this->crearPromptParaExtraccion($textoOCR);
            $key = config('services.gemini.api_key');
$response = Http::timeout(60)
            ->retry(3, 1000)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => config('services.gemini.api_key')
            ])
            ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent', [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'maxOutputTokens' => 1000,
                    'topP' => 0.8,
                    'topK' => 40
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_NONE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_HATE_SPEECH', 
                        'threshold' => 'BLOCK_NONE'
                    ]
                ]
            ]);

            if ($response->successful()) {
 $data = $response->json();
            
            // Extraer el texto de la respuesta (estructura puede variar)
            $jsonString = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                // Limpiar y parsear JSON
                return $this->parsearJSONRespuesta($jsonString);
            }
        } catch (\Exception $e) {
            Log::error("Error con API DeepSeek: " . $e->getMessage());
        }

        return $this->extraerDatosBasicos($textoOCR); // Fallback
    }

    private function crearPromptParaExtraccion(string $textoOCR): string
    {
        return <<<PROMPT
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
PROMPT;
    }

    private function parsearJSONRespuesta(string $jsonString): array
    {
        // Limpiar posibles markdown o código
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

    private function extraerDatosBasicos(string $textoOCR): array
    {
        // Fallback: extracción básica con regex si falla la IA
        preg_match('/(\$|MXN\s*)(\d+\.?\d*)/', $textoOCR, $matchesMonto);
        $monto = $matchesMonto[2] ?? null;

        // Lógica básica para establecercimiento (primeras líneas)
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
