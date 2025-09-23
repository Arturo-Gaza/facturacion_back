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
            $solicitud->update(['texto_ocr' => $textoOCR]);
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

}
