<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\SolicitudRepositoryInterface;
use App\Models\CatDatosPorGiro;
use App\Models\CatEmpresa;
use App\Models\CatGiro;
use App\Models\DatosFiscal;
use App\Models\SistemaTickets\CatEstatusSolicitud;
use App\Models\SistemaTickets\TabBitacoraSolicitud;
use App\Models\Solicitud;
use App\Models\SolicitudDatoAdicional;
use App\Models\SolicitudDatoGiro;
use App\Models\User;
use App\Services\AIDataExtractionService;
use App\Services\OCRService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            $cat_giros = CatGiro::all()->toArray();


            // Prepara los parámetros
            $parameters = [
                'cat_giro' => json_encode($cat_giros, JSON_PRETTY_PRINT),
            ];

            // Extraer datos estructurados con IA
            $datosExtraidos = $this->aiService->extractStructuredData($textoOCR, "receipt_extraction", $parameters, "receipt_extraction", $parameters, "receipt_extraction", $parameters);
            $datosAdicionales = $datosExtraidos['datos_facturacion_adicionales'];

            DB::transaction(function () use ($solicitud, $datosAdicionales) {

                // 2. Iterar sobre el array de datos adicionales (clave => valor)
                foreach ($datosAdicionales as $etiqueta => $valor) {

                    // 3. Opcional: Sanitizar la etiqueta y el valor si es necesario
                    $etiquetaLimpia = str_replace(['_', '-'], ' ', strtolower($etiqueta));
                    $etiquetaFormato = ucwords($etiquetaLimpia);

                    // 4. Crear un nuevo registro en la tabla 'solicitud_dato_adicional'
                    SolicitudDatoAdicional::create([
                        'id_solicitud' => $solicitud->id, // Usamos la ID de la solicitud principal
                        'etiqueta' => $etiquetaFormato, // Ejemplo: "Codigo Facturacion Idw"
                        'valor' => $valor                  // Ejemplo: "001 E94E 65V9 CR4X GFQK"
                    ]);
                }
            });

            $rfcExtraido = $datosExtraidos['rfc'] ?? null;
            $rfc = $rfcExtraido ? strtoupper(preg_replace('/\s+/', '', $rfcExtraido)) : null;

            $nombreExtraido = $datosExtraidos['nombre_empresa'] ?? $datosExtraidos['establecimiento'] ?? null;
            $urlExtraida = $datosExtraidos['url_facturacion'] ?? null;

            DB::beginTransaction();
            try {
                $empresa = null;

                // 1) Buscar por RFC (preferible)
                if ($rfc) {
                    $empresa = CatEmpresa::where('rfc', $rfc)->first();
                }

                // 2) Si no encontró por RFC y hay nombre, intentar buscar por nombre parecido
                if (!$empresa && $nombreExtraido) {
                    $empresa = CatEmpresa::where('nombre_empresa', 'LIKE', '%' . mb_strtolower($nombreExtraido) . '%')->first();
                }

                // 3) Si no existe, crear una entrada en catálogo
                if (!$empresa) {
                    // intentar obtener id_giro si IA devolvió algo
                    $idGiro = $datosExtraidos['id_giro'];


                    $empresa = CatEmpresa::create([
                        'rfc' => $rfc ?? null,
                        'nombre_empresa' => $nombreExtraido ? mb_convert_case($nombreExtraido, MB_CASE_TITLE) : null,
                        'pagina_web' => $urlExtraida ?? null,
                        'id_giro' => $idGiro,
                        'activo' => true,
                    ]);
                }

                // Definir los valores finales a guardar en la solicitud
                $urlFacturacionFinal = $empresa->pagina_web ?? $urlExtraida ?? null;
                $establecimientoFinal = $empresa->nombre_empresa ?? $nombreExtraido ?? null;

                // Actualizar solicitud con lo extraído / resuelto
                $solicitud->update([
                    'num_ticket' => $datosExtraidos['num_ticket'] ?? null,
                    'texto_ocr' => $textoOCR,
                    'establecimiento' => $establecimientoFinal,
                    'url_facturacion' => $urlFacturacionFinal,
                    'monto' => $datosExtraidos['monto'] ?? null,
                    'texto_json' => json_encode($datosExtraidos),
                    'fecha_ticket' => $datosExtraidos['fecha'],
                ]);
                /*
                if (!empty($empresa->id_giro)) {
                    $datosGiroGuardados = $this->extractDatosPorGiroAndSave($solicitud->id, $empresa->id_giro, $textoOCR);
                    // opcional: guardar un JSON resumen en solicitud
                    $solicitud->update([
                        'texto_json' => json_encode(array_merge(json_decode($solicitud->texto_json ?? '{}', true) ?? [], ['datos_giro' => $datosGiroGuardados]))
                    ]);
                }
                */


                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                // opcional: loguear $e->getMessage()
                // no abortamos; devolvemos la solicitud sin los cambios extra si quieres
            }
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
    public function asignar($id_user, $id_solicitud, $id_empleado)
    {
        $solicitud = Solicitud::find($id_solicitud);

        if (!$solicitud) {
            throw new \Exception('La solicitud no existe');
        }
        $id_estatus = $solicitud->estatestado_idus;
        // Verificar que el estatus sea 2
        if (!in_array($solicitud->estado_id, [2, 3])) {
            throw new \Exception('No se puede asignar la solicitud. El estatus no lo permite');
        }
        $solicitud->update([
            'estado_id' => 3,
            'empleado_id' => $id_empleado
        ]);

        TabBitacoraSolicitud::create([
            'id_solicitud' => $id_solicitud,
            'id_estatus' => 3, // Asumiendo que 2 es el ID del estatus "Enviado"
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
    public function getFacturaPDF($id_solicitud)
    {
                $solicitud = Solicitud::find($id_solicitud);

        if (!$solicitud) {
            Log::error("Solicitud no encontrada: {$id_solicitud}");
            return null;
        }

        if (
            !$solicitud->pdf_url ||
            !Storage::disk('public')->exists($solicitud->getRawOriginal('pdf_url'))
        ) {
            Log::error("PDF no encontrado para solicitud: {$solicitud->id}");
            return null;
        }

        try {
            $pdfPath = Storage::disk('public')->path($solicitud->getRawOriginal('pdf_url'));
            $pdfData = base64_encode(file_get_contents($pdfPath));
            return $pdfData;
        } catch (\Exception $e) {
            Log::error("Error al leer el PDF de la solicitud {$solicitud->id}: " . $e->getMessage());
            return null;
        }
    }
    public function getFacturaXML($id_solicitud)
    {
                $solicitud = Solicitud::find($id_solicitud);

        if (!$solicitud) {
            Log::error("Solicitud no encontrada: {$id_solicitud}");
            return null;
        }

        if (
            !$solicitud->xml_url ||
            !Storage::disk('public')->exists($solicitud->getRawOriginal('xml_url'))
        ) {
            Log::error("XML no encontrado para solicitud: {$solicitud->id}");
            return null;
        }

        try {
            $xmlPath = Storage::disk('public')->path($solicitud->getRawOriginal('xml_url'));
            $xmlData = base64_encode(file_get_contents($xmlPath));
            return $xmlData;
        } catch (\Exception $e) {
            Log::error("Error al leer el XML de la solicitud {$solicitud->id}: " . $e->getMessage());
            return null;
        }
    }
    public function getMesaAyuda()
    {
        return User::whereIn('idRol', [4, 5])->get();
    }

    public function getDashboard($idUsr)
    {
        $usr = User::find($idUsr);
        $fechaInicio = now()->subDays(30);

        // Obtener todos los estatus del catálogo
        $estatus = CatEstatusSolicitud::select('id', 'descripcion_estatus_solicitud')->get();

        // Construir consulta base con filtros por rol
        $query = Solicitud::where('created_at', '>=', $fechaInicio);

        // Aplicar filtro por rol
        if ($usr->idRol != 1 && $usr->idRol != 4) {
            $query->where('empleado_id', $idUsr);
        }

        // Select dinámico para estatus
        $selects = ['COUNT(*) as total_tickets'];
        foreach ($estatus as $estatusItem) {
            $selects[] = "SUM(CASE WHEN estado_id = {$estatusItem->id} THEN 1 ELSE 0 END) as tickets_estatus_{$estatusItem->id}";
        }

        $estadisticas = $query->selectRaw(implode(', ', $selects))->first();
        $totalTickets = $estadisticas->total_tickets ?? 0;

        // Estadísticas por estatus
        $estadisticasPorEstatus = [];
        foreach ($estatus as $estatusItem) {
            $campo = "tickets_estatus_{$estatusItem->id}";
            $cantidad = $estadisticas->$campo ?? 0;

            $estadisticasPorEstatus[] = [
                'estatus_id' => $estatusItem->id,
                'descripcion_estatus_solicitud' => $estatusItem->descripcion_estatus_solicitud,
                'total_tickets' => (int)$cantidad,
                'porcentaje' => $totalTickets > 0 ? round(($cantidad / $totalTickets) * 100, 2) : 0
            ];
        }

        // --------------------------2da parte estadistica por año-------------------------------------

        $queryMensual = Solicitud::selectRaw("
        EXTRACT(YEAR FROM solicitudes.created_at)::int as anio,
        EXTRACT(MONTH FROM solicitudes.created_at)::int as mes,
        solicitudes.estado_id,
        cat_estatus_solicitud.descripcion_estatus_solicitud as nombre_estatus,
        COUNT(*) as total
    ")
            ->join('cat_estatus_solicitud', 'cat_estatus_solicitud.id', '=', 'solicitudes.estado_id')
            ->groupByRaw('EXTRACT(YEAR FROM solicitudes.created_at), EXTRACT(MONTH FROM solicitudes.created_at), solicitudes.estado_id, cat_estatus_solicitud.descripcion_estatus_solicitud')
            ->orderByRaw('EXTRACT(YEAR FROM solicitudes.created_at), EXTRACT(MONTH FROM solicitudes.created_at)');

        if ($usr->idRol != 1 && $usr->idRol != 4) {
            $queryMensual->where('solicitudes.empleado_id', $idUsr);
        }

        $resultadosMensuales = $queryMensual->get();

        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $dataAnual = [];

        foreach ($resultadosMensuales as $r) {
            $nombreMes = $meses[$r->mes - 1];
            $anio = $r->anio;

            if (!isset($dataAnual[$anio])) {
                $dataAnual[$anio] = [
                    'anio' => $anio,
                    'meses' => []
                ];
            }

            if (!isset($dataAnual[$anio]['meses'][$nombreMes])) {
                $dataAnual[$anio]['meses'][$nombreMes] = [
                    'mes' => $nombreMes,
                    'estatus' => []
                ];
            }

            $dataAnual[$anio]['meses'][$nombreMes]['estatus'][] = [
                'estado_id' => $r->estado_id,
                'nombre_estatus' => $r->nombre_estatus,
                'total' => $r->total
            ];
        }

        // Formatear estructura final
        $dataAgrupada = array_values(array_map(function ($anioData) {
            $anioData['meses'] = array_values($anioData['meses']);
            return $anioData;
        }, $dataAnual));

        /////// como se envia la informacion
        return [
            'total_tickets' => $totalTickets,
            'estadisticas_por_estatus' => $estadisticasPorEstatus,
            'periodo' => 'ultimos_30_dias',
            'fecha_inicio' => $fechaInicio->format('Y-m-d H:i:s'),
            'fecha_fin' => now()->format('Y-m-d H:i:s'),
            'data_mensual' => $dataAgrupada // esta contiene los datos por año y mes
        ];
    }


    //     public function getDashboard($idUsr)
    //     {
    //         $usr = User::find($idUsr);
    //         $fechaInicio = now()->subDays(30);

    //         // Obtener todos los estatus del catálogo
    //         $estatus = CatEstatusSolicitud::select('id', 'descripcion_estatus_solicitud')->get();

    //         // Consulta base para los últimos 30 días
    //         $query = Solicitud::where('created_at', '>=', $fechaInicio);

    //         // Filtro por rol
    //         if ($usr->idRol != 1 && $usr->idRol != 4) {
    //             $query->where('empleado_id', $idUsr);
    //         }

    //         // Selects dinámicos
    //         $selects = ['COUNT(*) as total_tickets'];
    //         foreach ($estatus as $estatusItem) {
    //             $selects[] = "SUM(CASE WHEN estado_id = {$estatusItem->id} THEN 1 ELSE 0 END) as tickets_estatus_{$estatusItem->id}";
    //         }

    //         $estadisticas = $query->selectRaw(implode(', ', $selects))->first();
    //         $totalTickets = $estadisticas->total_tickets ?? 0;

    //         // Calcular métricas por estatus
    //         $estadisticasPorEstatus = [];
    //         foreach ($estatus as $estatusItem) {
    //             $campo = "tickets_estatus_{$estatusItem->id}";
    //             $cantidad = $estadisticas->$campo ?? 0;

    //             $estadisticasPorEstatus[] = [
    //                 'estatus_id' => $estatusItem->id,
    //                 'descripcion_estatus_solicitud' => $estatusItem->descripcion_estatus_solicitud,
    //                 'total_tickets' => (int)$cantidad,
    //                 'porcentaje' => $totalTickets > 0 ? round(($cantidad / $totalTickets) * 100, 2) : 0
    //             ];
    //         }

    //         // ---------------------------------------------------------------
    //         // SEGUNDA PARTE: Estadísticas mensuales (últimos 6 meses)
    //         // ---------------------------------------------------------------
    //         $fechaInicioMensual = now()->subMonths(6);

    //         $queryMensual = Solicitud::selectRaw("
    //     EXTRACT(MONTH FROM solicitudes.created_at)::int as mes,
    //     solicitudes.estado_id,
    //     cat_estatus_solicitud.descripcion_estatus_solicitud as nombre_estatus,
    //     COUNT(*) as total
    // ")
    //             ->join('cat_estatus_solicitud', 'cat_estatus_solicitud.id', '=', 'solicitudes.estado_id')
    //             ->where('solicitudes.created_at', '>=', $fechaInicioMensual)
    //             ->groupByRaw('EXTRACT(MONTH FROM solicitudes.created_at), solicitudes.estado_id, cat_estatus_solicitud.descripcion_estatus_solicitud')
    //             ->orderByRaw('EXTRACT(MONTH FROM solicitudes.created_at)');

    //         if ($usr->idRol != 1 && $usr->idRol != 4) {
    //             $queryMensual->where('solicitudes.empleado_id', $idUsr);
    //         }

    //         $resultadosMensuales = $queryMensual->get();

    //         $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    //         $dataMensual = [];

    //         foreach ($meses as $i => $nombreMes) {
    //             $dataMensual[$nombreMes] = [
    //                 'mes' => $nombreMes,
    //                 'estatus' => []
    //             ];
    //         }

    //         foreach ($resultadosMensuales as $r) {
    //             $nombreMes = $meses[$r->mes - 1];
    //             $dataMensual[$nombreMes]['estatus'][] = [
    //                 'estado_id' => $r->estado_id,
    //                 'nombre_estatus' => $r->nombre_estatus,
    //                 'total' => $r->total
    //             ];
    //         }


    //         // ---------------------------------------------------------------
    //         // Retorno completo
    //         // ---------------------------------------------------------------
    //         return [
    //             'total_tickets' => $totalTickets,
    //             'estadisticas_por_estatus' => $estadisticasPorEstatus,
    //             'periodo' => 'ultimos_30_dias',
    //             'fecha_inicio' => $fechaInicio->format('Y-m-d H:i:s'),
    //             'fecha_fin' => now()->format('Y-m-d H:i:s'),
    //             'data2' => array_values($dataMensual)
    //         ];
    //     }




    public function getByID($id): ?Solicitud
    {
        return Solicitud::with(['usuario', 'empleado', 'estadoSolicitud'])->find($id);
    }

    public function actualizarReceptor(Request $request)
    {
        $idSolicitud = $request->id_solicitud;
        $solicitud = Solicitud::findOrFail($idSolicitud);
        $idNuevoReceptor = $request->id_receptor;
        $Receptor = DatosFiscal::findOrFail($idNuevoReceptor);

        DB::transaction(function () use ($solicitud, $Receptor) {
            // Actualizar el receptor
            $solicitud->update([
                'id_receptor' => $Receptor->id,
                'id_regimen' => $Receptor->regimenPredeterminado->id_regimen,
                'usoCFDI' => $Receptor->uso_cfdi_predeterminado?->usoCFDI
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
            $solicitud->id_regimen = $usr->datosFiscalesPrincipal->regimenPredeterminado->id_regimen;
            $solicitud->usoCFDI = $usr->datosFiscalesPrincipal->uso_cfdi_predeterminado?->usoCFDI;
        } else {
            $solicitud->id_receptor = null; // o algún valor por defecto
            $solicitud->id_regimen = null;
            $solicitud->usoCFDI = null;
        }



        // Guardar imagen
        if ($request->hasFile('imagen')) {
            $rutaImagen = $solicitud->guardarImagen($request->file('imagen'), $id_user);
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

    public function actualizarEstatus($id_solicitud, $id_estatus, $id_usuario)
    {

        $solicitud = new Solicitud();
        $solicitud = Solicitud::find($id_solicitud);
        $solicitud->estado_id = $id_estatus; // Estado por defecto
        $solicitud->save();
        TabBitacoraSolicitud::create([
            'id_solicitud' => $id_solicitud,
            'id_estatus' => $id_estatus,
            'id_usuario' => $id_usuario
        ]);
    }
    public function update(array $data, $id): ?Solicitud
    {
        $solicitud = Solicitud::find($id);
        if ($solicitud) {
            $solicitud->update($data);
        }
        return $solicitud;
    }
    public function editarTicket(array $data, $id): ?Solicitud
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
    public function subirFactura($idUsr, $pdf, $xml, $id_solicitud)
    {
        $sol = Solicitud::find($id_solicitud);
        $rutaPdf = $sol->guardarPDF($pdf, $idUsr);
        $sol->pdf_url = $rutaPdf;
        $rutaXML = $sol->guardarXML($xml, $idUsr);
        $sol->xml_url = $rutaXML;
        $sol->estado_id = 6;
        $sol->save();
        return $sol;
    }

    public function getConsola($idUsr)
    {
        $usr = User::find($idUsr);
        if ($usr->idRol == 4 || $usr->idRol == 1) {
            $solicitudes = Solicitud::with(['empleado', 'estadoSolicitud', 'bitacora', 'datosGiro'])
                ->orderBy('updated_at', 'desc')
                ->get();
        }
        if ($usr->idRol == 5) {
            $solicitudes = Solicitud::with(['empleado', 'estadoSolicitud', 'bitacora', 'datosGiro'])
                ->orderBy('updated_at', 'desc')
                ->where('empleado_id', $idUsr)
                ->get();
        }

        $solicitudesFormateadas = $solicitudes->map(function ($solicitud) {
            $estatusCatalogo = CatEstatusSolicitud::pluck('descripcion_estatus_solicitud', 'id')->toArray();
            $nombreEstatus = $estatusCatalogo[$solicitud->estado_id] ?? 'Desconocido';
            // --- PREPARACIÓN: Definir todas las posibles claves de fecha inicializadas a null ---
            $clavesFechaNull = [];
            foreach ($estatusCatalogo as $descripcion) {
                $clave = 'fecha_hora_' . Str::snake($descripcion);
                $claveCorta = 'fecha_hora_corta_' . Str::snake($descripcion);
                $clavesFechaNull[$clave] = null;
                $clavesFechaNull[$claveCorta] = null;
            }
            $formatUsr = null;
            $nombreAsignado = $solicitud->empleado->nombre ?? null;
            if (!empty($solicitud->empleado_id)) {
                $formatUsr = sprintf('USR%03d', $solicitud->empleado_id);
            }

            $fechasDinamicas = $clavesFechaNull;

            // Agrupar los registros de bitácora por id_estatus y obtener el registro más antiguo (primera vez que entró en ese estado)
            $bitacoraPorEstado = $solicitud->bitacora
                ->sortBy('created_at') // Ordenar por fecha para encontrar la primera ocurrencia
                ->groupBy('id_estatus');

            foreach ($bitacoraPorEstado as $id_estatus => $registros) {
                // Obtener el nombre del estado del catálogo
                $nombreEstado = $estatusCatalogo[$id_estatus];

                if ($nombreEstado) {
                    // Limpiar el nombre del estado para usarlo como clave (ej: "En Proceso" -> "en_proceso")
                    $clave = 'fecha_hora_' . Str::snake($nombreEstado);
                    $claveCorta = 'fecha_hora_corta_' . Str::snake($nombreEstado);

                    // Obtener la fecha del primer registro para ese estado
                    $fecha = $registros->first()->created_at;

                    $fechasDinamicas[$clave] = $this->formatearFecha($fecha);
                    $fechasDinamicas[$claveCorta] = $this->formatearFechaCorta($fecha);
                }
            }
            $datosGiro = $solicitud->datosGiro->map(function ($item) {
                return [
                    'nombre' => $item->etiqueta ?? null,
                    'valor'  => $item->valor,
                ];
            });
            return array_merge([
                'id' => $solicitud->id,
                'ticket' => $solicitud->num_ticket ?? $solicitud->id,
                'establecimiento' => $solicitud->establecimiento,
                'fecha_ticket' => $solicitud->fecha_ticket,
                'usuario' =>   $formatUsr,
                'asignado_a' => $nombreAsignado,
                'estado_id' => $solicitud->estado_id,
                'nombre_estado' => $nombreEstatus,
                'url_facturacion' => $solicitud->url_facturacion,
                'monto' => $solicitud->monto,
                'idreceptor' => $solicitud->id_receptor,
                'datos_por_giro' => $datosGiro

            ], $fechasDinamicas);
        });
        // helper para formatear id de usuario como USR### (con ceros)
        return $solicitudesFormateadas;
    }
    public function formatearFecha($fecha)
    {
        return Carbon::parse($fecha)
            ->locale('es')
            ->translatedFormat('j-M-Y H:i');
    }

    public function formatearFechaCorta($fecha)
    {
        return Carbon::parse($fecha)->toISOString();
        // Ejemplo: "2024-01-15T10:30:00.000000Z"
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



    public function extractDatosPorGiroAndSave(int $id_solicitud, int $id_giro, string $textoOCR)
    {
        // Obtener definiciones de datos para el giro
        $campos = CatDatosPorGiro::where('id_giro', $id_giro)->get();
        if ($campos->isEmpty()) {
            return [];
        }

        // Preparar array que se inyectará en el prompt (la plantilla espera {$datos_por_giro})
        $camposParaPrompt = $campos->map(function ($c) {
            return [
                'name' => $c->nombre_dato_giro,
                'label' => $c->label ?? $c->nombre_dato_giro,
                'type' => $c->tipo ?? 'string',
                'required' => (bool)$c->requerido
            ];
        })->values()->toArray();

        // parámetros que tu extractStructuredData ya sabe sustituir en la plantilla
        $parameters = [
            'datos_por_giro' => json_encode($camposParaPrompt, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'id' => $id_solicitud,
            'textoOCR' => $textoOCR,
        ];

        // Llamada a tu servicio (usa el type que registraste en la BDD)
        try {
            $datos = $this->aiService->extractStructuredData($textoOCR, "datos_por_giro_extraction", $parameters);
        } catch (\Exception $e) {
            Log::error("Error IA datos por giro: " . $e->getMessage(), ['id_solicitud' => $id_solicitud, 'id_giro' => $id_giro]);
            return [];
        }



        // Guardar/actualizar cada campo en la tabla relacional
        $guardados = [];
        foreach ($campos as $campo) {
            $clave = $campo->nombre_dato_giro;
            $valor = array_key_exists($clave, $datos) ? $datos[$clave] : null;

            // Normalizaciones básicas según tipo
            if ($valor !== null) {
                if ($campo->tipo === 'numeric') {
                    // limpiar símbolos y comas, convertir a numero
                    $valor = preg_replace('/[^\d\.\-]/', '', (string)$valor);
                    $valor = $valor === '' ? null : (is_numeric($valor) ? (float)$valor : $valor);
                } else {
                    // trim strings
                    if (is_string($valor)) $valor = trim($valor);
                }
            }

            $registro = SolicitudDatoGiro::updateOrCreate(
                [
                    'id_solicitud' => $id_solicitud,
                    'id_dato_por_giro' => $campo->id
                ],
                [
                    'valor' => $valor
                ]
            );

            $guardados[$clave] = $registro->valor;
        }

        return $guardados;
    }

    /**
     * helper: extrae el primer bloque JSON en un texto (si viene con explicación)
     */
    private function extractJsonFromText(string $text): ?string
    {
        $start = strpos($text, '{');
        if ($start === false) return null;

        $braces = 0;
        $len = strlen($text);
        for ($i = $start; $i < $len; $i++) {
            if ($text[$i] === '{') $braces++;
            if ($text[$i] === '}') $braces--;
            if ($braces === 0) {
                return substr($text, $start, $i - $start + 1);
            }
        }
        return null;
    }
}
