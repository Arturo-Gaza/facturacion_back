<?php

namespace App\Repositories;


use App\Interfaces\TabSolicitudesRepositoryInterface;
use App\Mail\MandarCorreo;
use App\Models\SistemaTickets\CatDepartamentos;
use App\Models\SistemaTickets\CatEstatusSolicitud;
use App\Models\SistemaTickets\TabBitacoraSolicitud;
use App\Models\SistemaTickets\TabCotizacionesSolicitudes;
use App\Models\SistemaTickets\TabCotizacionesSolicitudesDetalle;
use App\Models\SistemaTickets\TabSolicitud;
use App\Models\SistemaTickets\TabSolicitudDetalle;
use App\Models\User;
use App\Services\EmailService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class TabSolicitudesRepository implements TabSolicitudesRepositoryInterface
{

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function getAll($id)
    {
        $user = User::find($id);

        $rolId = $user->rol ? $user->rol->id : null;


        switch ($rolId) {
            case 1:
                // Rol: "Administrador General"
                $solicitudes = TabSolicitud::orderByRaw("
    CASE id_estatus_solicitud
        WHEN 1 THEN 1  -- Capturada
        WHEN 2 THEN 2  -- Enviada
        WHEN 10 THEN 3 -- Recibida
        WHEN 7 THEN 4  -- Cotizando
        WHEN 8 THEN 5  -- En Proceso
        WHEN 6 THEN 6  -- Respuesta al requerimiento
        WHEN 5 THEN 7  -- Requiere información
        WHEN 4 THEN 8  -- Concluida
        WHEN 3 THEN 9  -- Cancelada
        ELSE 100       -- Otros al final
    END
")->orderBy('id', 'ASC')->get();

                break;

            case 2:
                // Rol: "Administrador Compras"
                $solicitudes = TabSolicitud::where('id_estatus_solicitud', '!=', 1)
                    ->orderByRaw("
            CASE id_estatus_solicitud
                WHEN 1 THEN 1
                WHEN 2 THEN 2
                WHEN 10 THEN 3
                WHEN 7 THEN 4
                WHEN 8 THEN 5
                WHEN 6 THEN 6
                WHEN 5 THEN 7
                WHEN 4 THEN 8
                WHEN 3 THEN 9
                ELSE 100
            END
        ")
                    ->orderBy('id', 'ASC')
                    ->get()
                    ->map(function ($sol) {
                        if ($sol->id_estatus_solicitud === 2) {
                            $sol->id_estatus_solicitud = 10;
                        }
                        return $sol;
                    });
                break;

            case 3:
                // Rol: "Usuario Compras"
                $solicitudes = TabSolicitud::where('id_usuario_asignacion', $user->id)
                    ->orderByRaw("
            CASE id_estatus_solicitud
                WHEN 1 THEN 1  -- Capturada
                WHEN 2 THEN 2  -- Enviada
                WHEN 10 THEN 3 -- Recibida
                WHEN 7 THEN 4  -- Cotizando
                WHEN 8 THEN 5  -- En Proceso
                WHEN 6 THEN 6  -- Respuesta al requerimiento
                WHEN 5 THEN 7  -- Requiere información
                WHEN 4 THEN 8  -- Concluida
                WHEN 3 THEN 9  -- Cancelada
                ELSE 100
            END
        ")
                    ->orderBy('id', 'ASC')
                    ->get()
                    ->map(function ($sol) {
                        if ($sol->id_estatus_solicitud === 2) {
                            $sol->id_estatus_solicitud = 10;
                        }
                        return $sol;
                    });
                break;

            case 4:
                // Rol: "Usuario General"
            case 4:
                // Rol: "Usuario General"
                $solicitudes = TabSolicitud::where('id_usuario_solicitud', $user->id)
                    ->orderByRaw("
            CASE id_estatus_solicitud
                WHEN 1 THEN 1  -- Capturada
                WHEN 2 THEN 2  -- Enviada
                WHEN 10 THEN 3 -- Recibida
                WHEN 7 THEN 4  -- Cotizando
                WHEN 8 THEN 5  -- En Proceso
                WHEN 6 THEN 6  -- Respuesta al requerimiento
                WHEN 5 THEN 7  -- Requiere información
                WHEN 4 THEN 8  -- Concluida
                WHEN 3 THEN 9  -- Cancelada
                ELSE 100
            END
        ")
                    ->orderBy('id', 'ASC')
                    ->get()
                    ->map(function ($sol) {
                        if ($sol->id_estatus_solicitud === 6) {
                            $sol->id_estatus_solicitud = 2;
                        }
                        return $sol;
                    });
                break;

            default:
                // Rol "Sin rol"
                return null;
        }


        return  $solicitudes;
    }

    public function getByID($id): ?TabSolicitud
    {
        $solicitud = TabSolicitud::with('archivosCotizaciones', 'detalles')  // eager load
            ->find($id);

        if ($solicitud) {
            // agrega el accessor para que aparezca en toArray()/JSON
            $solicitud->append('archivos_cotizaciones');
        }

        return $solicitud;
    }


    public function store(array $data)
    {

        // Crear la solicitud
        $solicitud = TabSolicitud::create($data);
        // Registrar en la bitácora
        TabBitacoraSolicitud::create([
            'id_solicitud' => $solicitud->id,
            'id_estatus' => 1, // por ejemplo: 1 = Creada
            'id_usuario' => $solicitud->id_usuario_solicitud
        ]);

        $datos = [
            'ticket' => $solicitud->id,
            'id_estatus' => 1
        ];

        $estatus = CatEstatusSolicitud::find(1);
        if ($estatus->mandarCorreo) {

            $this->emailService->enviarCorreoSolicitud($datos);
        }

        //return 'Correo enviado correctamente';


        return $solicitud;
    }

    public function asignar($data)
    {

        $id_solicitud = $data["id_solicitud"];
        $id_usuario_que_asigna = $data["id_usuario_que_asigna"];
        $solicitud = TabSolicitud::findOrFail($id_solicitud);
        $usr = User::findOrFail($id_usuario_que_asigna);
        $id_departamento = $usr->id_departamento;
        $departamento = CatDepartamentos::findOrFail($id_departamento);


        $id_usuario = $departamento->id_usuario_responsable_compras;
        // Actualizar el campo
        $solicitud->id_usuario_asignacion = $id_usuario;
        $solicitud->id_estatus_solicitud = 2;
        $solicitud->save();

        TabBitacoraSolicitud::create([
            'id_solicitud' => $id_solicitud,
            'id_estatus' => 2,
            'id_usuario' => $id_usuario_que_asigna
        ]);

        $datos = [
            'ticket' => $solicitud->id,
            'id_estatus' => 2
        ];

        $estatus = CatEstatusSolicitud::find(2);
        if ($estatus->mandarCorreo) {

            $this->emailService->enviarCorreoSolicitud($datos);
        }
        return $solicitud;
    }

    public function reasignar($data)
    {

        $id_solicitud = $data["id_solicitud"];
        $id_usuario = $data["id_usuario_que_asigna"];
        $solicitud = TabSolicitud::findOrFail($id_solicitud);

        // Actualizar el campo
        $solicitud->id_usuario_asignacion = $id_usuario;
        $solicitud->id_estatus_solicitud = 2;
        $solicitud->save();



        $datos = [
            'ticket' => $solicitud->id,
            'id_estatus' => 2
        ];

        $estatus = CatEstatusSolicitud::find(2);
        if ($estatus->mandarCorreo) {

            $this->emailService->enviarCorreoSolicitud($datos);
        }
        return $solicitud;
    }

    public function cambiarEstatus($data)
    {
        $id_usuario = $data["id_usuario_que_cambia"];
        $id_estatus = $data["id_estatus"];
        $id_solicitud = $data["id_solicitud"];

        $solicitud = TabSolicitud::findOrFail($id_solicitud);


        // Actualizar el campo
        $solicitud->id_estatus_solicitud = $id_estatus;
        $solicitud->save();

        TabBitacoraSolicitud::create([
            'id_solicitud' => $id_solicitud,
            'id_estatus' => $id_estatus,
            'id_usuario' => $id_usuario
        ]);

        $datos = [
            'ticket' => $solicitud->id,
            'id_estatus' => $id_estatus
        ];
        $estatus = CatEstatusSolicitud::find($id_estatus);
        if ($estatus->mandarCorreo) {

            $this->emailService->enviarCorreoSolicitud($datos);
        }
        return $solicitud;
    }

    public function update(array $data, $id)
    {
        return TabSolicitud::where('id', $id)->update($data);
    }
    public function reporte($ids, $filtros)
    {
        $solicitudes = TabSolicitud::whereIn('id', $ids)
            ->orderBy('updated_at', 'desc')
            ->get();


        // Ejecuta la consulta y aplica transformaciones (si son necesarias)


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $etiquetaFiltros = "";
        foreach ($filtros as $clave => $valor) {
            if (!empty($valor)) {
                $etiquetas[] = $clave . ' "' . $valor . '"';
            }
        }


        if (!empty($etiquetas)) {
            $row = 2;
            $etiquetaFiltros = 'Filtros aplicados: ' . implode(', ', $etiquetas);
            $sheet->mergeCells('A1:F1');
            $sheet->setCellValue('A1', $etiquetaFiltros);
            $sheet->getStyle('A1')->getFont()->setBold(true);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A1')->getAlignment()->setVertical('center');
            $sheet->getRowDimension(1)->setRowHeight(20);
        } else {
            $row = 1;
        }

        // Cabeceras
        $sheet->setCellValue("A{$row}", 'Ticket');
        $sheet->setCellValue("B{$row}", 'Usuario solicitante');
        $sheet->setCellValue("C{$row}", 'Usuario comprador');
        $sheet->setCellValue("D{$row}", 'Departamento');
        $sheet->setCellValue("E{$row}", 'Descripcion');
        $sheet->setCellValue("F{$row}", 'Prioridad');
        $sheet->setCellValue("G{$row}", 'Fecha de creación');
        $sheet->setCellValue("H{$row}", 'Fecha de envio');
        $sheet->setCellValue("I{$row}", 'Fecha de requerimiento de información');
        $sheet->setCellValue("J{$row}", 'Fecha de respuesta  al requerimiento de información');
        $sheet->setCellValue("K{$row}", 'Fecha de conclusión');
        $sheet->setCellValue("L{$row}", 'Fecha de cancelación');
        $sheet->setCellValue("M{$row}", 'Usiario que canceló');

        $row++;

        // Datos

        foreach ($solicitudes as $solicitud) {
            $sheet->setCellValue("A{$row}", $solicitud->id);
            $sheet->setCellValue("B{$row}", $solicitud->user_solicitud);
            $sheet->setCellValue("C{$row}", $solicitud->user_asignacion);
            $sheet->setCellValue("D{$row}", $solicitud->descripcion_departamento);
            $sheet->setCellValue("E{$row}", $solicitud->descripcion);
            $sheet->setCellValue("F{$row}", $solicitud->prioridad_valor);

            $bitacoras = TabBitacoraSolicitud::where('id_solicitud', $solicitud->id)->orderBy('created_at', 'ASC')->get();


            $row2 = $row - 1;
            foreach ($bitacoras as $bitacora) {
                $formattedDate = $bitacora->created_at
                    ? Carbon::parse($bitacora->created_at)->translatedFormat('d-F-Y H:i')
                    : '';
                switch ($bitacora->id_estatus) {
                    case 1:
                        $sheet->setCellValue("G{$row}", $formattedDate);
                        break;
                    case 2:
                        $sheet->setCellValue("H{$row}", $formattedDate);
                        break;
                    case 5:
                        $row2++;
                        $sheet->setCellValue("I{$row2}", $formattedDate);

                        break;
                    case 6:
                        $row2++;
                        $sheet->setCellValue("J{$row2}", $formattedDate);

                        break;
                    case 4:
                        $sheet->setCellValue("K{$row}", $formattedDate);
                        break;
                    case 3:
                        $sheet->setCellValue("L{$row}", $formattedDate);
                        $sheet->setCellValue("M{$row}", $bitacora->user);
                        break;
                }
            }
            if ($row2 == $row - 1) {
                $row = $row2 + 2;
            } else {
                $row = $row2 + 1;
            }
        }
        foreach (range('A', 'Z') as $column) {
            if ($column === 'E') {
                // Ancho fijo para la columna E
                $sheet->getColumnDimension($column)->setAutoSize(false);
                $sheet->getColumnDimension($column)->setWidth(50); // Aprox. 300 caracteres (ajustable)

                // Permitir que el texto se acomode en varias líneas
                $sheet->getStyle($column)->getAlignment()->setWrapText(true);
            } else {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
        }
        // Guardar en memoria
        $writer = new Xlsx($spreadsheet);
        $tempMemory = fopen('php://memory', 'r+');
        $writer->save($tempMemory);
        rewind($tempMemory);

        $excelContent = stream_get_contents($tempMemory);
        fclose($tempMemory);

        $base64 = base64_encode($excelContent);
        $data = [
            'file_name' => 'ReporteSolicitudes.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'base64' => $base64,
        ];
        return  $data;
    }
    public function formatearSolicitud($id)
    {
        $solicitud = TabSolicitud::find($id);
        if ($solicitud->cotizacion_global) {

            $cotizaciones = TabCotizacionesSolicitudes::where("id_solicitud", $solicitud->id)->get();
            foreach ($cotizaciones as $cotizacion) {
                $cotizacion->delete();
            }
            $solicitud->update();
        } else {

            $detalles = TabSolicitudDetalle::where("id_solicitud", $solicitud->id)->get();
            foreach ($detalles as $detalle) {
                $detalle->cotizado = null;
                $cotizaciones = TabCotizacionesSolicitudesDetalle::where("id_solicitud_detalle", $detalle->id)->get();
                foreach ($cotizaciones as $cotizacion) {
                    $cotizacion->delete();
                }
                $detalle->update();
            }
        }
        $solicitud->cotizacion_global = null;
        $solicitud->cotizadoGB = null;
        $solicitud->save();
        return  $solicitud;
    }
    public function getCotizaciones($id)
    {
        $solicitud = TabSolicitud::with([
            'archivosCotizaciones',
            'detalles.archivosCotizaciones'
        ])->findOrFail($id);
        if ($solicitud->cotizacion_global) {
            // Archivos globales
            $globales = $solicitud->archivosCotizaciones->map(function ($archivo) {
                return [
                    'descripcion_producto' => "Global",
                    'nombre_cotizacion' => $archivo->nombre_cotizacion,
                    'archivo_cotizacion' => $archivo->archivo_cotizacion,
                    'clave_producto' => "Global",
                    'recomendada' => $archivo->recomendada,
                    'created_at' => $archivo->created_at,
                    'justificacion' => $archivo->justificacion_general
                ];
            });
            return  $globales;
        } else {
            // Archivos por detalle
            $porDetalle = collect();
            foreach ($solicitud->detalles as $detalle) {
                foreach ($detalle->archivosCotizaciones as $archivo) {
                    $porDetalle->push([
                        'descripcion_producto' => $detalle->descripcion_producto,
                        'nombre_cotizacion' => $archivo->nombre_cotizacion,
                        'archivo_cotizacion' => $archivo->archivo_cotizacion,
                        'clave_producto' => $detalle->clave_producto,
                        'recomendada' => $archivo->recomendada,
                        'created_at' => $archivo->created_at,
                        'justificacion' => $archivo->justificacion

                    ]);
                }
            }
            return  $porDetalle;
        }
    }
}
