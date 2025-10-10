<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\SolicitudRepositoryInterface;
use App\Models\SistemaTickets\TabBitacoraSolicitud;
use App\Models\Solicitud;
use App\Models\User;
use App\Services\AIDataExtractionService;
use App\Services\OCRService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SolicitudRepository implements SolicitudRepositoryInterface
{

    private string $apiKey;
    private string $apiUrl;

    private OCRService $ocrService;
    private AIDataExtractionService $aiService;

    public function __construct(
        OCRService $ocrService = null,
        AIDataExtractionService $aiService = null
    ) {
        $this->ocrService = $ocrService ?: new OCRService();
        $this->aiService = $aiService ?: new AIDataExtractionService();
    }

    // ... (métodos existentes getAll, getByID, store, update, etc.)

    public function procesar(int $id_sol)
    {
        $solicitud = Solicitud::find($id_sol);

        if (!$solicitud) {
            return null;
        }

        // Procesar imagen con OCR
        $textoOCR = $this->procesarImagenConOCR($solicitud);

        if ($textoOCR) {
            // Extraer datos estructurados con IA
            $datosExtraidos = $this->aiService->extractStructuredData($textoOCR);

            $solicitud->update([
                'num_ticket' => $datosExtraidos['num_ticket'],
                'texto_ocr' => $textoOCR,
                'establecimiento' => $datosExtraidos['establecimiento'] ?? null,
                'monto' => $datosExtraidos['monto'] ?? null,
                'texto_json' => json_encode($datosExtraidos),
            ]);
        }

        return $solicitud->fresh();
    }
    public function enviar(int $id_sol, int $id_user)
    {
        $solicitud = Solicitud::find($id_sol);
        if (!$solicitud) {
            return null;
        }
        $solicitud->update([
            'estado_id' => 2
        ]);

        TabBitacoraSolicitud::create([
            'id_solicitud' => $id_sol,
            'id_estatus' => 2, // Asumiendo que 2 es el ID del estatus "Enviado"
            'id_usuario' => $id_user // O el ID del usuario que realiza la acción
        ]);
        return $solicitud->fresh();
    }
    public function eliminar(int $id_sol)
    {
        $solicitud = Solicitud::find($id_sol);
        if (!$solicitud) {
            return null;
        }
        $solicitud->update([
            'estado_id' => 5
        ]);
        return $solicitud->fresh();
    }

    private function procesarImagenConOCR(Solicitud $solicitud): ?string
    {
        try {
            if (!$solicitud->imagen_url || !Storage::disk('public')->exists($solicitud->getRawOriginal('imagen_url'))) {
                Log::error("Imagen no encontrada para solicitud: {$solicitud->id}");
                return null;
            }

            $imagePath = $solicitud->getRutaImagenAttribute();
            $imageData = base64_encode(file_get_contents($imagePath));

            return $this->ocrService->extractTextFromImage($imageData);
        } catch (\Exception $e) {
            Log::error("Error procesando imagen: " . $e->getMessage(), [
                'solicitud_id' => $solicitud->id
            ]);
            return null;
        }
    }


    public function getAll()
    {
        return Solicitud::with(['usuario', 'empleado', 'estadoSolicitud'])->get();
    }

    public function getByID($id): ?Solicitud
    {
        return Solicitud::with(['usuario', 'empleado', 'estadoSolicitud'])->find($id);
    }

    public function actualizarReceptor(Request $request)
    {
        $idSolicitud = $request->id_solicitud;
        $idNuevoReceptor = $request->id_receptor;
        $solicitud = Solicitud::findOrFail($idSolicitud);
        DB::transaction(function () use ($solicitud, $idNuevoReceptor) {
            // Actualizar el receptor
            $solicitud->update([
                'id_receptor' => $idNuevoReceptor
            ]);
        });
        return $solicitud;
    }

    public function store(Request $request,  $id_user): Solicitud
    {
        $solicitud = new Solicitud();
        $usr = User::find($request['usuario_id']);
        $solicitud->usuario_id = $request->usuario_id;
        $solicitud->estado_id = 1; // Estado por defecto

        if (isset($usr->datosFiscalesPrincipal) && isset($usr->datosFiscalesPrincipal->id)) {
            $solicitud->id_receptor = $usr->datosFiscalesPrincipal->id;
        } else {
            $solicitud->id_receptor = null; // o algún valor por defecto
        }
        $solicitud->id_regimen = $usr->datosFiscalesPrincipal->regimenPredeterminado->id_regimen;

        $solicitud->usoCFDI = $usr->datosFiscalesPrincipal->uso_cfdi_predeterminado?->usoCFDI;


        // Guardar imagen
        if ($request->hasFile('imagen')) {
            $rutaImagen = $solicitud->guardarImagen($request->file('imagen'));
            $solicitud->imagen_url = $rutaImagen;
        }
        $solicitud->save();
        $this->procesar($solicitud->id);
        $solicitud->save();
        TabBitacoraSolicitud::create([
            'id_solicitud' => $solicitud->id,
            'id_estatus' => 1,
            'id_usuario' => $id_user
        ]);

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
            ->whereNot('estado_id', 5)
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





    public function getGeneralByUsuario(int $usuario_id)
    {
        // Obtener conteos con los nombres de estado desde el catálogo
        $conteos = Solicitud::where('solicitudes.usuario_id', $usuario_id)
            ->whereNot('estado_id', 5)
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
}
