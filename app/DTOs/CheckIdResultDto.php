<?php

namespace App\DTOs;


use App\Models\Catalogos\CatRegimenesFiscales;
use Carbon\Carbon;

class CheckIdResultDto
{
    public ?int $id = null;
    public ?int $id_usuario = null;
    public ?string $rfc = null;
    public ?string $curp = null;
    public ?string $idCIF = null;
    public ?string $nombre_razon = null;
    public ?string $primer_apellido = null;
    public ?string $segundo_apellido = null;
    public ?string $nombre_completo = null;
    public ?string $fecha_inicio_op = null;
    public ?string $fecha_ult_cambio_op = null;
    public ?int $id_estatus_sat = null;
    public ?string $fecha_ultimo_cambio = null;
    public bool $es_persona_moral = false;
    public ?string $lugar_emision = null;
    public ?string $fecha_emision = null;
    public ?string $nombre_comercial = null;
    public ?string $email_facturacion_text = null;

    // domicilioFiscal as array
    public array $domicilioFiscal = [
        'codigo_postal' => null,
        'colonia' => null,
        'estado' => null,
        'localidad' => null,
        'municipio' => null,
        'calle' => null,
        'num_exterior' => null,
        'num_interior' => null,
        'pais' => 'México',
    ];

    public array $regimenesFiscales = [];
    public ?string $telefono = null;
    public $datos_extra = null;
    public $email_facturacion_id = null;
    public bool $habilitado = true;
    public bool $predeterminado = false;

    public function __construct(array $checkIdResponse = [])
    {
        // Asumimos que $checkIdResponse es la sección "resultado" de la API (o la raíz si vienen directo)
        $res = $checkIdResponse;

        // RFC section
        if (!empty($res['rfc']) && is_array($res['rfc'])) {
            $r = $res['rfc'];
            $this->rfc = $r['rfc'] ?? null;
            $this->nombre_razon = $r['razonSocial'] ?? null;
            // si curp viene desde rfc o curp
            $this->curp = $r['curp'] ?? ($res['curp']['curp'] ?? null);
            // email contacto como email_facturacion_text (si aplica)
            $this->email_facturacion_text = $r['emailContacto'] ?? null;

            // validoHasta -> fecha_emision / fecha_ultimo_cambio (ejemplo)
            if (!empty($r['validoHasta'])) {
                // convertimos a fecha Y-m-d
                try {
                    $d = Carbon::parse($r['validoHasta']);
                    $this->fecha_emision = $d->toDateString();
                } catch (\Throwable $e) {
                    $this->fecha_emision = null;
                }
            }

            // Detectar si es persona moral (simple heurística: si razonSocial contiene espacio y mayúsculas? mejor dejar false por defecto)
            $this->es_persona_moral = !empty($r["rfcRepresentante"]);
        }

        // CURP section (persona física data)
        if (!empty($res['curp']) && is_array($res['curp'])) {
            $c = $res['curp'];
            $this->curp = $c['curp'] ?? $this->curp;
            $this->curp = $this->es_persona_moral ? "" : $this->curp;
            $this->nombre_razon = $this->es_persona_moral
                ? $this->nombre_razon
                : ($c['nombres'] ?? $this->nombre_razon ?? '');
            $this->primer_apellido = $c['primerApellido'] ?? null;
            $this->segundo_apellido = $c['segundoApellido'] ?? null;
            $this->nombre_completo = trim(($c['nombres'] ?? '') . ' ' . ($c['primerApellido'] ?? '') . ' ' . ($c['segundoApellido'] ?? ''));
            // fechaNacimiento -> fecha_inicio_op (si quieres usarla así)
            if (!empty($c['fechaNacimiento'])) {
                try {
                    $dn = Carbon::parse($c['fechaNacimiento']);
                    $this->fecha_inicio_op = $dn->toDateString();
                } catch (\Throwable $e) {
                    $this->fecha_inicio_op = null;
                }
            }
        }
        // Codigo postal -> domicilioFiscal.codigo_postal
        if (!empty($res['codigoPostal']) && is_array($res['codigoPostal'])) {
            $cp = $res['codigoPostal'];
            $this->domicilioFiscal['codigo_postal'] = $cp['codigoPostal'] ?? null;
            // No tenemos colonia/calley demas, el servicio CheckID solo devuelve CP en tu ejemplo.
            // Puedes obtener mas datos si tienes un lookup de CP -> colonia/estado/municipio
            // Por ahora intentamos mapear entidad si viene en curp
            $this->domicilioFiscal['estado'] =null;
        }

        // regimenFiscal -> regimenesFiscales
        if (!empty($res['regimenFiscal']) && is_array($res['regimenFiscal'])) {
            $rf = $res['regimenFiscal'];
            $regimenTxt = $rf['regimenesFiscales'] ?? null;

            if ($regimenTxt) {
                $partes = explode(" - ", $regimenTxt);
                $claveRegimen = trim($partes[0] ?? null);
                $regimenCat = null;
                if ($claveRegimen) {
                    $regimenCat = CatRegimenesFiscales::with('usosCfdi')
                        ->where('clave', $claveRegimen)
                        ->first();
                }
                // creamos un registro simple: id_regimen null/placeholder, predeterminado true
                $this->regimenesFiscales[] = [
                    'id_regimen' => $regimenCat?->id_regimen,
                    'predeterminado' => false,
                    'fecha_inicio_regimen' => Carbon::now()->toDateString(),
                    'usosCfdi' => [],
                    'descripcion' => $regimenTxt,
                ];
            }
        }

        // NSS
        if (!empty($res['nss']) && is_array($res['nss'])) {
            $this->datos_extra = array_merge($this->datos_extra ?? [], ['nss' => $res['nss']['nss'] ?? null]);
        }

        // estado69o69B -> podemos mapear a id_estatus_sat si quieres
        if (!empty($res['estado69o69B']) && is_array($res['estado69o69B'])) {
            $this->id_estatus_sat = ($res['estado69o69B']['conProblema'] ?? false) ? 2 : 1;
        }

        // telefono: no viene en el ejemplo -> queda null
        $this->telefono = $this->telefono ?? null;

        // habilitado: si rfc.valido true -> habilitado true
        if (!empty($res['rfc']['valido'])) {
            $this->habilitado = (bool) $res['rfc']['valido'];
        }

        // idCIF - si existe algún campo identificador que quieras mapear, por ahora null
        $this->idCIF = $this->idCIF ?? null;

        // campos de fechas generales
        $this->fecha_ult_cambio_op = $this->fecha_ult_cambio_op ?? null;
        $this->fecha_ultimo_cambio = $this->fecha_ultimo_cambio ?? null;
    }

    /**
     * Retorna la representación final en el formato que pediste.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => [
                'id' => $this->id,
                'id_usuario' => $this->id_usuario,
                'rfc' => $this->rfc,
                'curp' => $this->curp,
                'idCIF' => $this->idCIF,
                'nombre_razon' => $this->nombre_razon,
                'primer_apellido' => $this->primer_apellido,
                'segundo_apellido' => $this->segundo_apellido,
                'nombre_completo' => $this->nombre_completo,
                'fecha_inicio_op' => $this->fecha_inicio_op,
                'fecha_ult_cambio_op' => $this->fecha_ult_cambio_op,
                'id_estatus_sat' => $this->id_estatus_sat,
                'fecha_ultimo_cambio' => $this->fecha_ultimo_cambio,
                'es_persona_moral' => $this->es_persona_moral,
                'lugar_emision' => $this->lugar_emision,
                'fecha_emision' => $this->fecha_emision,
                'nombre_comercial' => $this->nombre_comercial,
                'email_facturacion_text' => $this->email_facturacion_text,
                'domicilioFiscal' => $this->domicilioFiscal,
                'regimenesFiscales' => $this->regimenesFiscales,
                'telefono' => $this->telefono,
                'datos_extra' => $this->datos_extra,
                'email_facturacion_id' => $this->email_facturacion_id,
                'habilitado' => $this->habilitado,
                'predeterminado' => $this->predeterminado,
            ],
            'data2' => null,
            'message' => 'Datos fiscales obtenidos',
        ];
    }
}
