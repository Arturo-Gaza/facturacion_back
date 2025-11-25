<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\SolicitudRepositoryInterface;
use App\Models\ArchivoCarga\TabObservaciones;
use App\Models\CatDatosPorGiro;
use App\Models\CatEmpresa;
use App\Models\CatGiro;
use App\Models\CatMotivoRechazo;
use App\Models\DatosFiscal;
use App\Models\MovimientoSaldo;
use App\Models\Precio;
use App\Models\SistemaTickets\CatEstatusSolicitud;
use App\Models\SistemaTickets\TabBitacoraSolicitud;
use App\Models\SistemaTickets\TabObservacionesSolicitudes;
use App\Models\Solicitud;
use App\Models\SolicitudDatoAdicional;
use App\Models\SolicitudDatoGiro;
use App\Models\Suscripciones;
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
use App\Services\EmailService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\File;

class SolicitudRepository implements SolicitudRepositoryInterface
{

    private string $apiKey;
    protected $emailService;
    private string $apiUrl;

    private OCRService $ocrService;
    private AIDataExtractionService $aiService;

    public function __construct(
        OCRService $ocrService = null,
        AIDataExtractionService $aiService = null,
        EmailService $emailService
    ) {
        $this->ocrService = $ocrService ?: new OCRService();
        $this->aiService = $aiService ?: new AIDataExtractionService();
        $this->emailService = $emailService;
    }

    // ... (métodos existentes getAll, getByID, store, update, etc.)
    public function validar($datosExtraidos, $solicitud)
    {
        $exito = $datosExtraidos["exito"];
        $idFueraVigencia = 1;
        $idFueraTiempo = 4;
        if (!$exito) {
            $id_motivo_rechazo = isset($datosExtraidos["motivo_de_rechazo"])
                ? $datosExtraidos["motivo_de_rechazo"]
                : null;
            if ($id_motivo_rechazo) {
                $motivo_rechazo = CatMotivoRechazo::find($id_motivo_rechazo);
            } else {
                $motivo_rechazo = CatMotivoRechazo::find(2);
            }
            $solicitud->eliminarImagen();
            $solicitud->delete();
            throw new Exception($motivo_rechazo->detalle);
        } else {
            if ($datosExtraidos["fecha"] && env("APLICA_VIGENCIA")) {
                $fechaTicket = Carbon::parse($datosExtraidos["fecha"]);
                $mesTicket = $fechaTicket->format('Y-m');
                $mesActual = Carbon::now()->format('Y-m');

                if ($mesTicket !== $mesActual) {
                    $motivo_rechazo = CatMotivoRechazo::find($idFueraVigencia);
                    $solicitud->eliminarImagen();
                    $solicitud->delete();
                    throw new Exception($motivo_rechazo->detalle);
                }
            }
            if ($datosExtraidos["fecha"] && env("APLICA_FUERA_TIEMPO") && env("HORAS_FUERA_TIEMPO")) {
                $fechaTicket = Carbon::parse($datosExtraidos["fecha"]);
                $fechaLimite = $fechaTicket->addHours(env("HORAS_FUERA_TIEMPO"));
                $finDeMes = Carbon::now()->endOfMonth();

                if ($fechaLimite->gt($finDeMes)) {
                    $motivo_rechazo = CatMotivoRechazo::find($idFueraTiempo);
                    $solicitud->eliminarImagen();
                    $solicitud->delete();
                    throw new Exception($motivo_rechazo->detalle);
                }
            }
        }
        return null;
    }
    public function procesar(int $id_sol)
    {
        $report = [
            'id_solicitud' => $id_sol,
            'found' => false,
            'ocr_text' => null,
            'ai_result' => null,
            'datos_adicionales_count' => 0,
            'empresa' => null,
            'update_attempted' => false,
            'update_result' => null,
            'exception' => null,
            'steps' => [],
        ];

        $solicitud = Solicitud::find($id_sol);
        if (!$solicitud) {
            $report['steps'][] = 'Solicitud no encontrada';
            return $report;
        }
        $report['found'] = true;

        // Procesar imagen con OCR
        try {
            $textoOCR = $this->procesarImagenConOCR($solicitud);
            $report['ocr_text'] = $textoOCR;
            $report['steps'][] = 'OCR completado';
        } catch (\Throwable $e) {
            $report['exception'] = 'OCR error: ' . $e->getMessage();
            $report['steps'][] = 'OCR falló';
            return $report;
        }

        if (!$textoOCR) {
            $report['steps'][] = 'OCR devolvió vacío';
            return $report;
        }

        // Preparar catálogos para IA
        $cat_giros = CatGiro::all()->toArray();
        $cat_motivos_rechazo = CatMotivoRechazo::where('validar_por_IA', true)
            ->where('activo', true)
            ->get()
            ->toArray();

        $parameters = [
            'cat_giro' => json_encode($cat_giros, JSON_PRETTY_PRINT),
            'cat_motivos_rechazo' => json_encode($cat_motivos_rechazo, JSON_PRETTY_PRINT),
            'fecha' => Carbon::now()->format('Y-m-d')
        ];

        // Intentar extracción IA
        try {
            $datosExtraidos = $this->aiService->extractStructuredData($textoOCR, "receipt_extraction", $parameters);
            $report['ai_result'] = $datosExtraidos;
            $report['steps'][] = 'IA completada';
        } catch (\Throwable $e) {
            $report['exception'] = 'IA error: ' . $e->getMessage();
            $report['steps'][] = 'IA falló';
            return $report;
        }

        // Guardar datos adicionales en su propia transacción (o unificada; elige lo que prefieras)
        $datosAdicionales = $datosExtraidos['datos_facturacion_adicionales'] ?? [];
        $report['datos_adicionales_count'] = count($datosAdicionales);

        try {
            DB::transaction(function () use ($solicitud, $datosAdicionales, &$report) {
                foreach ($datosAdicionales as $etiqueta => $valor) {
                    $etiquetaLimpia = str_replace(['_', '-'], ' ', strtolower($etiqueta));
                    $etiquetaFormato = ucwords($etiquetaLimpia);

                    SolicitudDatoAdicional::create([
                        'id_solicitud' => $solicitud->id,
                        'etiqueta' => $etiquetaFormato,
                        'valor' => $valor
                    ]);
                }
                $report['steps'][] = 'Datos adicionales guardados';
            });
        } catch (\Throwable $e) {
            $report['exception'] = 'Error guardando datos adicionales: ' . $e->getMessage();
            $report['steps'][] = 'Guardado datos adicionales falló';
            // no abortamos, devolvemos el report con info
        }

        // Normalizar RFC, nombre, url
        $rfcExtraido = $datosExtraidos['rfc'] ?? null;
        $rfc = $rfcExtraido ? strtoupper(trim(preg_replace('/\s+/', '', $rfcExtraido))) : null;

        // Asegurarse: si quedó cadena vacía, poner null
        if ($rfc !== null && $rfc === '') {
            $rfc = null;
        }
        $nombreExtraido = $datosExtraidos['nombre_empresa'] ?? $datosExtraidos['establecimiento'] ?? null;
        $urlExtraida = $datosExtraidos['url_facturacion'] ?? null;
        $idGiro = $datosExtraidos['id_giro'] ?? null;

        // Usar una transacción para la creación/actualización final
        try {
            DB::beginTransaction();

            $empresa = null;
            if ($rfc) {
                $empresa = CatEmpresa::where('rfc', $rfc)->first();
                $report['steps'][] = $empresa ? 'Empresa encontrada por RFC' : 'No encontrada por RFC';
            }

            if (!$empresa && $nombreExtraido) {
                $empresa = CatEmpresa::where('nombre_empresa', 'LIKE', '%' . mb_strtolower($nombreExtraido) . '%')->first();
                $report['steps'][] = $empresa ? 'Empresa encontrada por nombre' : 'No encontrada por nombre';
            }

            if (!$empresa) {
                $empresa = CatEmpresa::create([
                    'rfc' => $rfc ?? null,
                    'nombre_empresa' => $nombreExtraido ? mb_convert_case($nombreExtraido, MB_CASE_TITLE) : null,
                    'pagina_web' => $urlExtraida ?? null,
                    'id_giro' => $idGiro,
                    'activo' => true,
                ]);
                $report['steps'][] = 'Empresa creada';
            }

            $report['empresa'] = ['id' => $empresa->id ?? null, 'nombre' => $empresa->nombre_empresa ?? null];

            // Preparar array a actualizar
            $payload = [
                'num_ticket' => $datosExtraidos['num_ticket'] ?? null,
                'texto_ocr' => $textoOCR,
                'establecimiento' => $empresa->nombre_empresa ?? $nombreExtraido ?? null,
                'url_facturacion' => $empresa->pagina_web ?? $urlExtraida ?? null,
                'monto' => $datosExtraidos['monto'] ?? null,
                'texto_json' => json_encode($datosExtraidos),
                'fecha_ticket' => $datosExtraidos['fecha'] ?? null,
                'id_empresa' => $empresa->id ?? null
            ];

            // Chequeo: ver si hay campos que causen problemas (ej. fecha mal formateada)
            $report['payload_preview'] = $payload;

            // Intentar update (mass-assignment puede fallar)
            $report['update_attempted'] = true;

            // Forma segura: usar fill + save para obtener booleano de save()
            $solicitud->fill($payload);
            $saveResult = $solicitud->save(); // devuelve true/false

            $report['update_result'] = $saveResult;
            $report['was_changed'] = $solicitud->wasChanged();
            $report['changed_fields'] = $solicitud->getChanges();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $report['exception'] = 'Error actualización/empresa: ' . $e->getMessage();
            $report['steps'][] = 'Actualización falló y se hizo rollback';
        }

        // Refrescar modelo y devolver diagnostico
        $solicitud->refresh();
        $report['solicitud_actual'] = $solicitud->toArray();

        return $report;
    }

    public function revertir($id_usuario, $id_solicitud)
    {
        return DB::transaction(function () use ($id_usuario, $id_solicitud) {
            $sol = Solicitud::find($id_solicitud);

            if (! $sol) {
                throw new ModelNotFoundException("Solicitud con id {$id_solicitud} no encontrada.");
            }


            // Alternativa: buscar la bitácora inmediata anterior a la fecha en que se puso el 6.
            // Si no hay nada, usar un estatus por defecto (ej. 1)
            $estatusRestaurar = 5;

            // Borrar archivos si existen
            $paths = [
                $sol->getRawOriginal('pdf_url'),
                $sol->getRawOriginal('xml_url')
            ];

            foreach ($paths as $p) {
                if ($p && Storage::disk('public')->exists($p)) {
                    Storage::disk('public')->delete($p);
                }
            }

            // Actualizar modelo
            $sol->pdf_url = null;
            $sol->xml_url = null;
            $sol->estado_id = $estatusRestaurar;
            $sol->save();

            // Registrar en bitácora la reversión
            TabBitacoraSolicitud::create([
                'id_solicitud' => $id_solicitud,
                'id_estatus' => $estatusRestaurar,
                'id_usuario' => $id_usuario,
            ]);

            return $sol;
        });
    }

    public function enviar(int $id_sol, int $id_user)
    {
        DB::beginTransaction();

        try {
            $solicitud = Solicitud::find($id_sol);
            if (!$solicitud) {
                DB::rollBack();
                return null;
            }

            // Actualizar estado de la solicitud
            $solicitud->update([
                'estado_id' => 2
            ]);

            // Crear registro en bitácora
            TabBitacoraSolicitud::create([
                'id_solicitud' => $id_sol,
                'id_estatus' => 2,
                'id_usuario' => $id_user
            ]);

            $user = User::find($id_user);
            $efectivoUsuario = $user->usuario_padre
                ? User::find($user->usuario_padre) ?? $user
                : $user;
            $estatusPendienteId = 7;

            // Calcular precio y obtener datos de cobro
            $datosCobro = $this->calcularPrecio($id_sol, $id_user);
            if ($datosCobro["insuficiente_saldo"]) {
                throw new Exception(("Saldo insuiciente"));
            }
            $monto_a_cobrar = $datosCobro["monto_a_cobrar"];

            // Crear movimiento de saldo
            $mov = MovimientoSaldo::create([
                'tipo' => "cargo",
                'usuario_id' => $id_user,
                'monto' => $monto_a_cobrar,
                'currency' => env('DIVISA', 'mxn'),
                'amount_cents' => $monto_a_cobrar * 100,
                'estatus_movimiento_id' => $estatusPendienteId,
                'saldo_antes' => $datosCobro["saldo_actual"],
                'saldo_resultante' => $datosCobro["saldo_despues"],
                'descripcion' => sprintf(
                    "Cobro de %s %s por la facturación del ticket %s",
                    $monto_a_cobrar,
                    env('DIVISA', 'mxn'),
                    $solicitud->num_ticket
                ),
                'refunded_amount' => 0,
                'reverted' => false,
                'id_solicitud' => $solicitud->id
            ]);

            // Actualizar saldo del usuario
            $efectivoUsuario->saldo = $datosCobro["saldo_despues"];


            // Incrementar contador de facturas
            $this->incrementarFacturasRealizadas($efectivoUsuario);
            $efectivoUsuario->save();
            DB::commit();

            return $datosCobro["saldo_despues"];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en enviar solicitud: ' . $e->getMessage());

            throw new Exception($e->getMessage());
        }
    }

    public function incrementarFacturasRealizadas(User $user)
    {
        try {


            $suscripcion = $user->suscripcionActiva ?? Suscripciones::where('usuario_id', $user->id)->latest()->first();

            if ($suscripcion) {
                $suscripcion->increment('facturas_realizadas');
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error al incrementar facturas realizadas: ' . $e->getMessage());
            return false;
        }
    }

    public function decrementarFacturasRealizadas(User $user)
    {
        try {


            $suscripcion = $user->suscripcionActiva ?? Suscripciones::where('usuario_id', $user->id)->latest()->first();

            if ($suscripcion && $suscripcion->facturas_realizadas > 0) {
                $suscripcion->decrement('facturas_realizadas');
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error al decrementar facturas realizadas: ' . $e->getMessage());
            return false;
        }
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
        $solicitud->eliminarImagen();
        $solicitud->update([
            'estado_id' => 11
        ]);
        return $solicitud->fresh();
    }

    public function rechazar($id_solicitud, $id_motivo_rechazo, $id_user)
    {
        return DB::transaction(function () use ($id_solicitud, $id_motivo_rechazo, $id_user) {

            $solicitud = Solicitud::find($id_solicitud);
            if (!$solicitud) {
                throw new \Exception('Solicitud no encontrada.');
            }

            $motivo = CatMotivoRechazo::find($id_motivo_rechazo);
            if (!$motivo) {
                throw new \Exception('Motivo de rechazo no encontrado.');
            }

            // Evitar rechazar varias veces la misma solicitud
            if ($solicitud->estado_id === 7) {
                throw new \Exception('La solicitud ya fue rechazada.');
            }

            // Actualizar estado de solicitud
            $solicitud->update([
                'estado_id' => 7
            ]);

            // Registrar en bitácora
            TabBitacoraSolicitud::create([
                'id_solicitud' => $id_solicitud,
                'id_estatus'   => 7,
                'id_usuario'   => $id_user
            ]);

            // Registrar observación (guarda texto y referencia)
            TabObservacionesSolicitudes::create([
                'id_solicitud'          => $id_solicitud,
                'id_usuario'            => $id_user,
                'id_motivo_rechazo'     => $id_motivo_rechazo, // 👈 añade este campo en la tabla si aún no existe
                'observacion'           => "Rechazado por:" . $motivo->descripcion
            ]);
            $mov = MovimientoSaldo::where('id_solicitud', $id_solicitud)->first();

            $estatus_revertido = 6;
            if ($mov) {
                $user = User::find($mov->usuario_id);
                $efectivoUsuario = $user->usuario_padre
                    ? User::find($user->usuario_padre) ?? $user
                    : $user;
                $mov->estatus_movimiento_id = $estatus_revertido;

                $mov->descripcion = trim(($mov->descripcion ?? '') . ' (Rechazado por: ' . $motivo->descripcion . ')');

                $mov->save();
                if ($user) {
                    $this->decrementarFacturasRealizadas($efectivoUsuario); // crea esta función si no existe
                    $efectivoUsuario->saldo += $mov->monto; // devolver el saldo
                    $efectivoUsuario->save();
                }
            }

            return $solicitud->fresh();
        });
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
            // Extraer el archivo como tal
            $fileContent = file_get_contents($imagePath);
            $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

            if ($extension === 'pdf') {
                $file = new File($imagePath);
                $parameters = [
                    'id' => $solicitud->id
                ];
                return $this->aiService->extractTextFromPDF($file, 'texto_extraction', $parameters);
            } else {
                // Si es imagen
                $imageData = base64_encode($fileContent);
                return $this->ocrService->extractTextFromImage($imageData);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }


    public function getAll()
    {
        return Solicitud::with(['usuario', 'empleado', 'estadoSolicitud'])->get();
    }

    public function calcularPrecio($id_solicitud, $id_user)
    {
        $hoy = Carbon::now()->startOfDay();
        $user = User::find($id_user);
        $efectivoUsuario = $user->usuario_padre
            ? User::find($user->usuario_padre) ?? $user
            : $user;
        // Si no hay plan directo, intentar suscripción activa
        $plan = null;

        if ($efectivoUsuario->suscripcionActiva) {
            $plan = $efectivoUsuario->suscripcionActiva->plan;
        }
        if (!$plan) {
            $sus = $efectivoUsuario->planVencido;
            return [
                //'error' => 'No se encontró una suscripcion vigente para el usuario.',
                'tipo' => 'mensual',
                'monto_a_cobrar' => 0.00,
                'tier' => null,
                'saldo_actual' => (float) $efectivoUsuario->saldo,
                'saldo_despues' => (float) $efectivoUsuario->saldo,
                'insuficiente_saldo' => false,
                'factura_numero' => 0,
                'factura_restante' => 0,
                'fecha_vencimiento' => $sus?->fecha_vencimiento ?? null,
                'vigente' => (bool) false,
            ];
        }
        $suscripcion = $efectivoUsuario->suscripcionActiva ?? Suscripciones::where('usuario_id', $efectivoUsuario->id)->latest()->first();
        $num_factura = $suscripcion->facturas_realizadas + 1;
        $fecha_vencimiento = $suscripcion->fecha_vencimiento;
        $vigente = false;
        if ($suscripcion) {
            $vigente = $suscripcion->estaVigente();
        }
        if ($plan->esMensual()) {
            // Buscar suscripción activa/vigente (si aplica)

            $facturas_realizadas = $suscripcion->facturas_realizadas;
            return [
                'tipo' => 'mensual',
                'vigente' => (bool) $vigente,
                'monto_a_cobrar' => 0,
                'tier' => $plan->nombre_plan,
                'saldo_actual' => 0,
                'saldo_despues' => 0,
                'insuficiente_saldo' => (bool) !$vigente,
                'factura_numero' => $num_factura,
                'factura_restante' => 0,
                'fecha_vencimiento' => $fecha_vencimiento
            ];
        }
        $precioRegistro = Precio::where('id_plan', $plan->id)
            ->where(function ($q) use ($hoy) {
                $q->whereNull('vigencia_desde')->orWhere('vigencia_desde', '<=', $hoy);
            })
            ->where(function ($q) use ($hoy) {
                $q->whereNull('vigencia_hasta')->orWhere('vigencia_hasta', '>=', $hoy);
            })
            ->where(function ($q) use ($num_factura) {
                $q->whereNull('desde_factura')->orWhere('desde_factura', '<=', $num_factura);
            })
            ->where(function ($q) use ($num_factura) {
                $q->whereNull('hasta_factura')->orWhere('hasta_factura', '>=', $num_factura);
            })
            ->orderByDesc('vigencia_desde')
            ->first();

        // fallback al precio directo en la tabla cat_planes si existe
        $factura_restante  = $precioRegistro ? $precioRegistro->hasta_factura - $num_factura : null;
        $precioUnitario = $precioRegistro ? (float) $precioRegistro->precio : (float) ($plan->precio ?? 0.00);
        $vigencia_saldo = $user->fecha_vencimiento_saldo;
        if ($efectivoUsuario->saldo - $precioUnitario < 0 || ($vigencia_saldo < now() && $precioUnitario > 0)) {
            return [
                'tipo' => 'prepago',
                'monto_a_cobrar' =>  $precioUnitario,
                'vigente' => (bool) $vigente,
                'tier' => $precioRegistro->nombre_precio,
                'saldo_actual' => (float) $efectivoUsuario->saldo,
                'saldo_despues' => (float) $efectivoUsuario->saldo - $precioUnitario,
                'insuficiente_saldo' => true,
                'factura_numero' => $num_factura,
                'factura_restante' => $factura_restante,
                'facturaTotalGratis' => $precioRegistro->hasta_factura,
                '$vigencia_saldo' => $vigencia_saldo

            ];
        }
        return [
            'tipo' => 'prepago',
            'monto_a_cobrar' =>  $precioUnitario,
            'vigente' => (bool) $vigente,
            'tier' => $precioRegistro->nombre_precio,
            'saldo_actual' => (float) $efectivoUsuario->saldo,
            'saldo_despues' => (float) $efectivoUsuario->saldo - $precioUnitario,
            'insuficiente_saldo' => false,
            'factura_numero' => $num_factura,
            'factura_restante' => $factura_restante,
            'facturaTotalGratis' => $precioRegistro->hasta_factura,
            '$vigencia_saldo' => $vigencia_saldo
        ];
    }

    public function getTodosDatos($id)
    {
        $sol = Solicitud::find($id);
        $todo_datos_txt = $sol->texto_json;
        $data = json_decode($todo_datos_txt, true);
        if ($data === null || !isset($data['todos_datos'])) {
            // Manejar el caso de error o campo no encontrado
            error_log("Error al decodificar JSON o el campo 'todos_datos' no existe.");
            return null;
        }
        $todosDatos = $data['todos_datos'];


        $datosLimpios = array_filter($todosDatos, function ($value) {
            return $value !== null;
        });
        return $datosLimpios;
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
    public function mandarFactura($id_solicitud)
    {
        $solicitud = Solicitud::find($id_solicitud);
        $receptor = $solicitud->receptor ?? null;
        $email = null;
        if ($receptor) {
            $email = $receptor->email_facturacion_text;
        }
        if ($email) {
            $xmlRelativePath = $solicitud->getRawOriginal('xml_url');

            if (!empty($xmlRelativePath) && Storage::disk('public')->exists($xmlRelativePath)) {
                $xmlPath = Storage::disk('public')->path($xmlRelativePath);
                $archivos[] = $xmlPath;
                Log::info("Archivo XML encontrado: " . $xmlPath);
            } else {
                Log::warning("Archivo XML no disponible o no encontrado. Path: " . ($xmlRelativePath ?? 'NULL'));
            }

            // Para PDF - con verificación de null
            $pdfRelativePath = $solicitud->getRawOriginal('pdf_url');

            if (!empty($pdfRelativePath) && Storage::disk('public')->exists($pdfRelativePath)) {
                $pdfPath = Storage::disk('public')->path($pdfRelativePath);
                $archivos[] = $pdfPath;
                Log::info("Archivo PDF encontrado: " . $pdfPath);
            } else {
                Log::warning("Archivo PDF no disponible o no encontrado. Path: " . ($pdfRelativePath ?? 'NULL'));
            }

            // Verificar que tenemos archivos para enviar
            if (empty($archivos)) {
                Log::error("No hay archivos disponibles para adjuntar al correo");
                return "Error: No hay archivos disponibles para enviar";
            }
            $this->emailService->enviarCorreoFac($email, $archivos);
        } else {
            throw new Exception("Correo no encontrado");
        }
    }

    public function concluir($id_usuario, $id_solicitud)
    {
        $estatus_concluido = 9;
        $solicitud = Solicitud::find($id_solicitud);
        $solicitud->estado_id = 9; // Estado por defecto
        $solicitud->save();
        $id_user = $solicitud->usuario_id;
         $id_receptor = $solicitud->id_receptor;
        $user = User::find($id_user);
        $receptor=DatosFiscal::find($id_receptor);
        if ($user) {
            $email = $user->mailPrincipal->email;
        }
        if ($email) {
            $xmlRelativePath = $solicitud->getRawOriginal('xml_url');

            if (!empty($xmlRelativePath) && Storage::disk('public')->exists($xmlRelativePath)) {
                $xmlPath = Storage::disk('public')->path($xmlRelativePath);
                $archivos[] = $xmlPath;
                Log::info("Archivo XML encontrado: " . $xmlPath);
            } else {
                Log::warning("Archivo XML no disponible o no encontrado. Path: " . ($xmlRelativePath ?? 'NULL'));
            }

            // Para PDF - con verificación de null
            $pdfRelativePath = $solicitud->getRawOriginal('pdf_url');

            if (!empty($pdfRelativePath) && Storage::disk('public')->exists($pdfRelativePath)) {
                $pdfPath = Storage::disk('public')->path($pdfRelativePath);
                $archivos[] = $pdfPath;
                Log::info("Archivo PDF encontrado: " . $pdfPath);
            } else {
                Log::warning("Archivo PDF no disponible o no encontrado. Path: " . ($pdfRelativePath ?? 'NULL'));
            }

            // Verificar que tenemos archivos para enviar
            if (empty($archivos)) {
                Log::error("No hay archivos disponibles para adjuntar al correo");
                return "Error: No hay archivos disponibles para enviar";
            }
            $datosMail = [
                'email' => $email,
                'nombre'=>$user->nombre,
                'ticket'=>$solicitud ->num_ticket,
                'fecha_ticket'=>$solicitud ->fecha_ticket,
                'establecimiento'=>$solicitud ->establecimiento,
                'total'=>$solicitud->monto,
                'rfc_receptor'=>$receptor->rfc,
                'nombre_receptor'=>$receptor->nombre_razon
            ];
            $this->emailService->enviarCorreoFac($datosMail, $archivos);
        }
        TabBitacoraSolicitud::create([
            'id_solicitud' => $id_solicitud,
            'id_estatus' => $estatus_concluido,
            'id_usuario' => $id_usuario
        ]);
    }

    public function getDashboard($fecha_inicio, $fecha_fin, $idUsr)
    {
        $usr = User::find($idUsr);
        $fecha_inicio = $fecha_inicio
            ? Carbon::parse($fecha_inicio)
            : now()->subDays(30);

        $fecha_fin = $fecha_fin
            ? Carbon::parse($fecha_fin)
            : now();


        // Obtener todos los estatus del catálogo
        $estatus = CatEstatusSolicitud::select('id', 'descripcion_estatus_solicitud')->get();

        // Construir consulta base con filtros por rol
        $query = Solicitud::whereBetween('created_at', [$fecha_inicio, $fecha_fin]);


        // Aplicar filtro por rol
        if ($usr->idRol  == 5) {
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
        COUNT(*) as total")
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
            'fecha_inicio' => $fecha_inicio->format('Y-m-d H:i:s'),
            'fecha_fin' => $fecha_fin->format('Y-m-d H:i:s'),
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
        try {
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
        } catch (\Exception $e) {
            try {
                // Si el modelo se guardó y existe, eliminar imagen mediante el método que ya tienes
                if (isset($solicitud) && $solicitud->getKey()) {
                    // eliminar imagen física si existe
                    try {
                        $solicitud->eliminarImagen();
                    } catch (\Exception $imgEx) {
                        Log::error("Error eliminando imagen de solicitud {$solicitud->id}: " . $imgEx->getMessage());
                    }

                    // eliminar bitacoras creadas (por si se creó antes del error)
                    try {
                        TabBitacoraSolicitud::where('id_solicitud', $solicitud->id)->delete();
                    } catch (\Exception $bitEx) {
                        Log::error("Error eliminando bitacoras de solicitud {$solicitud->id}: " . $bitEx->getMessage());
                    }

                    // eliminar el registro de la solicitud (si no estás usando transacción superior,
                    // esto evita dejar un registro parcial en BD)
                    try {
                        $solicitud->delete();
                    } catch (\Exception $delEx) {
                        Log::error("Error eliminando registro solicitud {$solicitud->id}: " . $delEx->getMessage());
                    }
                } else {
                    // Si no hay modelo persistido, pero guardaste la imagen y conoces la ruta, bórrala.
                    if (!empty($rutaImagen) && Storage::disk('public')->exists($rutaImagen)) {
                        try {
                            Storage::disk('public')->delete($rutaImagen);
                        } catch (\Exception $stoEx) {
                            Log::error("Error eliminando archivo temporal {$rutaImagen}: " . $stoEx->getMessage());
                        }
                    }
                }
            } catch (\Exception $cleanupEx) {
                // Si la limpieza falla por alguna razón, lo registramos pero no ocultamos el error original.
                Log::error('Error durante limpieza en SolicitudRepository@store: ' . $cleanupEx->getMessage());
            }

            // Log del error original y re-lanzar para que el controlador (o quien llamó) lo maneje
            Log::error('Error en SolicitudRepository@store: ' . $e->getMessage());
            throw $e;
        }
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

        if (!empty($data['url_facturacion'])) {
            // Prioriza id_empresa proveniente en $data, si no intenta con el id guardado en la solicitud
            $empresaId = $data['id_empresa'] ?? $solicitud->id_empresa ?? null;

            if ($empresaId) {
                $empresa = CatEmpresa::find($empresaId);
                if ($empresa) {
                    $empresa->pagina_web = $data['url_facturacion'];
                    $empresa->save();
                }
            }
        }

        $solicitud = Solicitud::find($id);
        if ($solicitud) {
            $solicitud->update($data);
        }
        return $solicitud;
    }

    public function getByUsuario($fecha_inicio, $fecha_fin, int $usuario_id)
    {


        if ($usuario_id === -1) {
            // -1 indica "todo": tomar al usuario autenticado como raíz y todos sus hijos
            $rootId = auth()->user()->id;
            $ids = $this->getUserAndDescendantsIds($rootId);
        } else {
            // id específico: solo ese usuario (sin recursividad)
            $ids = [$usuario_id];
        }

        $fecha_inicio = $fecha_inicio
            ? Carbon::parse($fecha_inicio)
            : now()->subDays(30);

        $fecha_fin = $fecha_fin
            ? Carbon::parse($fecha_fin)
            : now();

        $solicitudes = Solicitud::whereIn('usuario_id', $ids)
            ->whereNot('estado_id', 11)
            ->whereBetween('solicitudes.created_at', [$fecha_inicio, $fecha_fin])
            ->with(['usuario', 'empleado', 'estadoSolicitud'])
            ->get();
        $solicitudes->each(function ($solicitud) {
            if ($solicitud->estadoSolicitud) {
                $solicitud->estadoSolicitud->descripcion_estatus_solicitud = $solicitud->estadoSolicitud->descripcion_cliente ?? $solicitud->estadoSolicitud->descripcion_estatus_solicitud;
                $solicitud->estadoSolicitud->color = $solicitud->estadoSolicitud->color_cliente ?? $solicitud->estadoSolicitud->color;
            }
        });
        return $solicitudes;
    }
    public function subirFactura($idUsr, $pdf, $xml, $id_solicitud)
    {
        $status = 6;
        $sol = Solicitud::find($id_solicitud);
        $rutaPdf = $sol->guardarPDF($pdf, $idUsr);
        $sol->pdf_url = $rutaPdf;
        $rutaXML = $sol->guardarXML($xml, $idUsr);
        $sol->xml_url = $rutaXML;
        $sol->estado_id = $status;
        TabBitacoraSolicitud::create([
            'id_solicitud' => $id_solicitud,
            'id_estatus' => $status, // Asumiendo que 2 es el ID del estatus "Enviado"
            'id_usuario' => $idUsr // O el ID del usuario que realiza la acción
        ]);
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
            $coloresCatalogo = CatEstatusSolicitud::pluck('color', 'id')->toArray();

            $nombreEstatus = $estatusCatalogo[$solicitud->estado_id] ?? 'Desconocido';
            $colorEstatus = $coloresCatalogo[$solicitud->estado_id] ?? '#CCCCCC'; // Color por defecto
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
                'color_estado' => $colorEstatus,
                'url_facturacion' => $solicitud->url_facturacion,
                'monto' => $solicitud->monto,
                'idreceptor' => $solicitud->id_receptor,
                'datos_por_giro' => $datosGiro,
                'id_empresa' => $solicitud->id_empresa

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


    function getUserAndDescendantsIds(int $rootId): array
    {
        $ids = [$rootId];
        $queue = [$rootId];

        while (!empty($queue)) {
            $current = array_shift($queue);
            $children = User::where('usuario_padre', $current)->pluck('id')->toArray();
            if (!empty($children)) {
                foreach ($children as $c) {
                    if (!in_array($c, $ids, true)) {
                        $ids[] = $c;
                        $queue[] = $c;
                    }
                }
            }
        }

        return $ids;
    }


    public function getGeneralByUsuario($fecha_inicio, $fecha_fin, $usuario_id)
    {
        $usuario_id = intval($usuario_id);
        if ($usuario_id === -1) {
            // -1 indica "todo": tomar al usuario autenticado como raíz y todos sus hijos
            $rootId = auth()->user()->id;
            $ids = $this->getUserAndDescendantsIds($rootId);
        } else {
            // id específico: solo ese usuario (sin recursividad)
            $ids = [$usuario_id];
        }

        $fecha_inicio = $fecha_inicio
            ? Carbon::parse($fecha_inicio)
            : now()->subDays(30);

        $fecha_fin = $fecha_fin
            ? Carbon::parse($fecha_fin)
            : now();

        $conteos = Solicitud::whereIn('solicitudes.usuario_id', $ids)
            ->whereBetween('solicitudes.created_at', [$fecha_inicio, $fecha_fin])
            ->join('cat_estatus_solicitud', 'solicitudes.estado_id', '=', 'cat_estatus_solicitud.id')
            ->selectRaw('cat_estatus_solicitud.id, cat_estatus_solicitud.descripcion_cliente as estado, COUNT(*) as count')
            ->groupBy('cat_estatus_solicitud.id', 'cat_estatus_solicitud.descripcion_cliente')
            ->get();

        $total = $conteos->sum('count');

        $estadisticasPorEstatus = $conteos->map(function ($item) use ($total) {
            return [
                'estatus_id' => $item->id,
                'descripcion_estatus_solicitud' => $item->estado,
                'total_tickets' => (int)$item->count,
                'porcentaje' => $total > 0 ? round(($item->count / $total) * 100, 2) : 0
            ];
        });

        // ---------------------- Parte 2: data_mensual ----------------------
        $queryMensual = Solicitud::selectRaw("
        EXTRACT(YEAR FROM solicitudes.created_at)::int as anio,
        EXTRACT(MONTH FROM solicitudes.created_at)::int as mes,
        solicitudes.estado_id,
        cat_estatus_solicitud.descripcion_cliente as nombre_estatus,
        COUNT(*) as total
    ")
            ->join('cat_estatus_solicitud', 'cat_estatus_solicitud.id', '=', 'solicitudes.estado_id')
            ->whereIn('solicitudes.usuario_id', $ids)
            ->whereBetween('solicitudes.created_at', [$fecha_inicio, $fecha_fin])
            ->groupByRaw('EXTRACT(YEAR FROM solicitudes.created_at), EXTRACT(MONTH FROM solicitudes.created_at), solicitudes.estado_id, cat_estatus_solicitud.descripcion_cliente')
            ->orderByRaw('EXTRACT(YEAR FROM solicitudes.created_at), EXTRACT(MONTH FROM solicitudes.created_at)');

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

        $dataAgrupada = array_values(array_map(function ($anioData) {
            $anioData['meses'] = array_values($anioData['meses']);
            return $anioData;
        }, $dataAnual));

        return [
            'total_tickets' => $total,
            'estadisticas_por_estatus' => $estadisticasPorEstatus, // ✅ agregado para el front
            'fecha_inicio' => $fecha_inicio->format('Y-m-d H:i:s'),
            'fecha_fin' => $fecha_fin->format('Y-m-d H:i:s'),
            'data_mensual' => $dataAgrupada
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
