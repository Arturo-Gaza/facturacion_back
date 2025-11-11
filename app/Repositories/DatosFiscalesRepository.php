<?php

namespace App\Repositories;

use App\DTOs\UserProfileDTO;
use App\Interfaces\DatosFiscalesRepositoryInterface;
use App\Models\Catalogos\CatEstatusesSat;
use App\Models\Catalogos\CatRegimenesFiscales;
use App\Models\DatosFiscal;
use App\Models\DatosFiscalRegimenFiscal;
use App\Models\DatosFiscalRegimenUsoCfdi;
use App\Models\Direccion;
use App\Models\Suscripciones;
use App\Models\User;
use App\Models\UsuarioRegimenFiscal;
use App\Services\AIDataExtractionService;
use App\Services\OCRService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatosFiscalesRepository implements DatosFiscalesRepositoryInterface
{
    private OCRService $ocrService;
    private AIDataExtractionService $aiService;

    public function __construct(
        OCRService $ocrService = null,
        AIDataExtractionService $aiService = null
    ) {
        $this->ocrService = $ocrService ?: new OCRService();
        $this->aiService = $aiService ?: new AIDataExtractionService();
    }

    public function getAll()
    {
        return DatosFiscal::with([
            'direcciones',
            'regimenesFiscales.regimen', // Relación con el catálogo de regímenes
            'regimenesFiscales.usosCfdi.usoCfdi', // Relación anidada con usos CFDI
            'regimenPredeterminado.regimen', // Régimen predeterminado
            'regimenPredeterminado.usosCfdi.usoCfdi', // Usos CFDI del régimen predeterminado
            'usoCfdiPredeterminado' // Uso CFDI predeterminado directo
        ])->get();
    }

    public function getByID($id): ?DatosFiscal
    {
        return DatosFiscal::with(
            'domicilioFiscal',
            'regimenesFiscales.usosCfdi'
        )->find($id);
    }

    public function getByUsr($id)
    {
        $user = User::find($id);

        if (!$user) {
            return []; // o lanzar excepción según tu estándar
        }

        // Si es usuario padre (rol 2)
        if ($user->idRol == 2 || $user->idRol == 1) {
            return DatosFiscal::with([
                'domicilioFiscal',
                'regimenesFiscales.usosCfdi'
            ])
                ->whereHas('usuario', function ($query) use ($id) {
                    $query->where('id', $id);
                })
                ->where('habilitado', true)
                ->whereDoesntHave('usuario', function ($query) {
                    $query->whereColumn('datos_fiscales_personal', 'datos_fiscales.id');
                })
                ->get();

            // Si es usuario hijo (rol 3)
        } elseif ($user->idRol == 3) {
            $data = $user->facturantesPermitidos()
                ->with([
                    'domicilioFiscal',
                    'regimenesFiscales.usosCfdi'
                ])
                ->where('habilitado', true)
                ->get();

            // Marcar cuál es el predeterminado
            $data->each(function ($facturante) use ($user) {
                $facturante->es_predeterminado = (bool) $facturante->pivot->predeterminado;
            });

            return $data;
        } else {
            return [];
        }
    }

    public function storeConDomicilio(array $data, array $direccion)
    {
        $datosFiscales = DatosFiscal::create($data);


        if ($direccion && $datosFiscales) {
            $direccion['id_fiscal'] = $datosFiscales->id;
            $direccion['id_tipo_direccion'] = 2;
            Direccion::create($direccion);
        }
        // Actualizar el usuario con los nuevos datos fiscales principales
        $user = User::Find($datosFiscales->id_usuario);
        $user->update([
            'datos_fiscales_personal' => $datosFiscales->id
        ]);
        // Recargar el usuario con las relaciones actualizadas
        $user->load(['datosFiscalesPrincipal', 'rol', 'departamento', 'mailPrincipal', 'telefonoPrincipal']);

        // Devolver el DTO
        return UserProfileDTO::fromUserModel($user);
    }

    public function storeCompleto(array $data, array $direccion, array $regimenes)
    {
        DB::beginTransaction();

        try {
            // Crear el dato fiscal
            $datosFiscales = DatosFiscal::create($data);
            // Guardar los regímenes fiscales
            $this->guardarRegimenesFiscales($regimenes, $datosFiscales);

            // Guardar la dirección
            if ($direccion && $datosFiscales) {
                $direccion['id_fiscal'] = $datosFiscales->id;
                $direccion['id_tipo_direccion'] = 1;
                Direccion::create($direccion);
            }
            $user = User::find($datosFiscales->id_usuario);
            //$correo = $user->mailPrincipal;
            //$datosFiscales->email_facturacion_id = $correo->id;
            //$datosFiscales->email_facturacion_text= $correo->email;
            $datosFiscales->save();
            if ($data["predeterminado"]) {
                // Actualizar el usuario con los nuevos datos fiscales principales


                $user->update([
                    'datos_fiscales_principal' => $datosFiscales->id
                ]);
            }
            DB::commit();

            // Devolver el DTO
            return UserProfileDTO::fromUserModel($user);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }



    public function updateCompleto(array $data, array $direccion, array $regimenes, $idDatosFiscales)
    {
        DB::beginTransaction();

        try {
            // Buscar los datos fiscales existentes
            $datosFiscales = DatosFiscal::findOrFail($idDatosFiscales);

            // Actualizar los datos fiscales
            $datosFiscales->update($data);

            // Actualizar los regímenes fiscales
            $this->actualizarRegimenesFiscales($regimenes, $datosFiscales);

            // Actualizar la dirección
            if ($direccion) {
                $direccionExistente = Direccion::where('id_fiscal', $datosFiscales->id)
                    ->where('id_tipo_direccion', 1)
                    ->first();

                if ($direccionExistente) {
                    $direccionExistente->update($direccion);
                } else {
                    // Crear nueva dirección si no existe
                    $direccion['id_fiscal'] = $datosFiscales->id;
                    $direccion['id_tipo_direccion'] = 1;
                    Direccion::create($direccion);
                }
            }

            $user = User::find($datosFiscales->id_usuario);

            // Actualizar datos fiscales principales si es necesario
            if ($data["predeterminado"]) {
                $user->update([
                    'datos_fiscales_principal' => $datosFiscales->id
                ]);
            } elseif ($user->datos_fiscales_principal == $datosFiscales->id) {
                $user->update([
                    'datos_fiscales_principal' => null
                ]);
            }

            DB::commit();

            // Cargar el modelo completo con todas las relaciones y ocultar timestamps
            $datosFiscalesCompleto = DatosFiscal::with([
                'domicilioFiscal',
                'regimenesFiscales.usosCfdi'
            ])->find($datosFiscales->id);



            return $datosFiscalesCompleto;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function actualizarRegimenesFiscales(array $regimenes, DatosFiscal $datosFiscales)
    {
        // Obtener IDs de regímenes actuales
        $regimenesActuales = $datosFiscales->regimenesFiscales()->pluck('id_regimen')->toArray();
        // Obtener IDs de regímenes nuevos
        $nuevosRegimenes = array_column($regimenes, 'id_regimen');

        // Encontrar regímenes a eliminar
        $eliminar = array_diff($regimenesActuales, $nuevosRegimenes);

        // Encontrar regímenes a agregar
        $agregar = array_diff($nuevosRegimenes, $regimenesActuales);

        // Eliminar regímenes
        if (!empty($eliminar)) {
            $datosFiscales->regimenesFiscales()
                ->whereIn('id_regimen', $eliminar)
                ->delete();
        }

        foreach ($agregar as $idRegimen) {
            $regimenCompleto = collect($regimenes)->firstWhere('id_regimen', $idRegimen);

            $datoFiscalRegimen = $datosFiscales->regimenesFiscales()->create([
                'id_regimen' => $idRegimen,
                'fecha_inicio_regimen' => $regimenCompleto['fecha_inicio_regimen'] ?? now()->format('Y-m-d'),
                'predeterminado' => $regimenCompleto['predeterminado'] ?? false,
            ]);

            // Guardar los usos CFDI para el nuevo régimen
            if (isset($regimenCompleto['usosCfdi'])) {
                $this->guardarUsosCfdiParaRegimen($datoFiscalRegimen, $regimenCompleto['usosCfdi']);
            }
        }
    }

    public function guardarRegimenesFiscales(array $regimenes, DatosFiscal $datosFiscales)
    {
        $idDatoFiscal = $datosFiscales->id;
        $idRegimenPredeterminado = null;

        foreach ($regimenes as $regimen) {
            $idRegimen = $regimen['id_regimen'];
            $esRegimenPredeterminado = $regimen['predeterminado'] ?? false;
            $usosCfdi = $regimen['usosCfdi'] ?? [];
            $fecha_inicio_regimen = $regimen['fecha_inicio_regimen'];

            // Crear el registro en datos_fiscales_regimenes_fiscales
            $datoFiscalRegimen = DatosFiscalRegimenFiscal::create([
                'id_dato_fiscal' => $idDatoFiscal,
                'id_regimen' => $idRegimen,
                'predeterminado' => $esRegimenPredeterminado,
                'fecha_inicio_regimen' => $fecha_inicio_regimen
            ]);

            // Si es el régimen predeterminado, guardar su ID
            if ($esRegimenPredeterminado) {
                $idRegimenPredeterminado = $datoFiscalRegimen->id;
            }

            // Guardar los usos CFDI para este régimen
            $this->guardarUsosCfdiParaRegimen($datoFiscalRegimen, $usosCfdi);
        }
    }

    private function guardarUsosCfdiParaRegimen(DatosFiscalRegimenFiscal $datoFiscalRegimen, array $usosCfdi)
    {
        $idUsoCfdiPredeterminado = null;
        $predeterminadosCount = 0;

        foreach ($usosCfdi as $usoCfdi) {
            $usoCfdiCodigo = $usoCfdi['uso_cfdi'];
            $esUsoPredeterminado = $usoCfdi['predeterminado'] ?? false;

            // Validar que solo haya un predeterminado por régimen
            if ($esUsoPredeterminado) {
                $predeterminadosCount++;
                if ($predeterminadosCount > 1) {
                    throw new \Exception("Solo puede haber un uso de CFDI predeterminado por régimen");
                }
                $idUsoCfdiPredeterminado = $usoCfdiCodigo;
            }

            // Crear el registro en datos_fiscales_regimen_usos_cfdi
            DatosFiscalRegimenUsoCfdi::create([
                'id_dato_fiscal_regimen' => $datoFiscalRegimen->id,
                'uso_cfdi' => $usoCfdiCodigo,
                'predeterminado' => $esUsoPredeterminado
            ]);
        }

        // Si no hay predeterminado en este régimen, establecer el primero como predeterminado
        if ($predeterminadosCount === 0 && count($usosCfdi) > 0) {
            $primerUsoCfdi = $usosCfdi[0]['uso_cfdi'];

            $primerRegistro = DatosFiscalRegimenUsoCfdi::where('id_dato_fiscal_regimen', $datoFiscalRegimen->id)
                ->where('uso_cfdi', $primerUsoCfdi)
                ->first();

            if ($primerRegistro) {
                $primerRegistro->update(['predeterminado' => true]);
            }
        }
    }

    public function store(array $data): DatosFiscal
    {
        $id_usuario = $data["id_usuario"];
        $usr = User::find($id_usuario);
        $correo = $usr->mailPrincipal;
        $data["email_facturacion_id"] = $correo->id;
        $data["email_facturacion_text"] = $correo->email;
        return DatosFiscal::create($data);
    }

    public function update(array $data, $id): ?DatosFiscal
    {
        $datosFiscales = DatosFiscal::find($id);
        if ($datosFiscales) {

            $datosFiscales->update($data);
        }
        return $datosFiscales;
    }
    public function eliminarReceptor($id)
    {
        $datosFiscales = DatosFiscal::find($id);

        if (!$datosFiscales) {
            throw new Exception("Receptor no encontrado");
        }

        if ($datosFiscales->predeterminado) {
            throw new Exception("Este receptor es el predeterminado, no puede ser eliminado");
        }


        // Opción 1: Deshabilitar
        $datosFiscales->habilitado = 0;
        $datosFiscales->save();

        // Opción 2: Si usas soft delete
        // $datosFiscales->delete();

        return $datosFiscales;
    }

    public function extraerDatosCFDI(Request $request)
    {
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
        }
        $textoExtraido = $this->extraerTextoDePDF($archivo, "cfdi_extraction");
        return $textoExtraido;
    }

    public function validarCantidadRFC($id_user)
    {
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
            return [
                'error' => 'No se encontró plan para el usuario.',
                'monto_a_cobrar' => 0.00,
                'tier' => null,
                'saldo_actual' => (float) $efectivoUsuario->saldo,
                'saldo_despues' => (float) $efectivoUsuario->saldo,
                'insuficiente_saldo' => false,
                'tipo' => null,
                'factura_numero' => 0,
                'factura_restante' => 0
            ];
        }
        $suscripcion = $efectivoUsuario->suscripcionActiva ?? Suscripciones::where('usuario_id', $efectivoUsuario->id)->latest()->first();
        $rfc_realizados = $suscripcion->rfc_realizados + 1;
        $rfc_permitidas = $plan->num_rfc;
        $rfc_restante = $plan->num_rfc - $rfc_realizados;

        $vigente = false;
        if ($suscripcion) {
            $vigente = $suscripcion->estaVigente();
        }
        if (!$rfc_permitidas) {

            return [
                'tipo' => 'mensual',
                'vigente' => (bool) $vigente,
                'monto_a_cobrar' => null,
                'tier' => $plan->nombre_plan,
                'saldo_actual' => null,
                'saldo_despues' => null,
                'rfc_suficiente' =>  true ,
                'rfc_numero' => $rfc_realizados,
                'rfc_restante' => $rfc_restante
            ];
        }

        return [
            'tipo' => 'mensual',
            'vigente' => (bool) $vigente,
            'monto_a_cobrar' => null,
            'tier' => $plan->nombre_plan,
            'saldo_actual' => null,
            'saldo_despues' => null,
            'rfc_suficiente' => $rfc_restante <= 0 ? true : false,
            'rfc_numero' => $rfc_realizados,
            'rfc_restante' => $rfc_restante
        ];
    }
    private function extraerTextoDePDF($archivo)
    {
        try {
            $regimenesFiscales = CatRegimenesFiscales::all()->toArray();
            $estatusSat = CatEstatusesSat::all()->toArray();

            // Prepara los parámetros
            $parameters = [
                'regimenesFiscales' => json_encode($regimenesFiscales, JSON_PRETTY_PRINT),
                'estatusSat' => json_encode($estatusSat, JSON_PRETTY_PRINT)
            ];

            $texto = $this->aiService->extractStructuredDataPDF($archivo, 'cfdi_extraction', $parameters);

            return $texto ?: '';
        } catch (\Exception $e) {

            throw $e;
        }
    }
}
