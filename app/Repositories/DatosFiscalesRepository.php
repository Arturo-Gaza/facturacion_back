<?php

namespace App\Repositories;

use App\DTOs\UserProfileDTO;
use App\Interfaces\DatosFiscalesRepositoryInterface;
use App\Models\DatosFiscal;
use App\Models\DatosFiscalRegimenFiscal;
use App\Models\DatosFiscalRegimenUsoCfdi;
use App\Models\Direccion;
use App\Models\User;
use App\Models\UsuarioRegimenFiscal;
use App\Services\AIDataExtractionService;
use App\Services\OCRService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Smalot\PdfParser\Parser;
use Spatie\PdfToText\Pdf;

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
        return DatosFiscal::with('direcciones')->find($id);
    }

    public function getByUsr($id)
    {
        return DatosFiscal::with('direcciones')
            ->whereHas('usuario', function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->whereDoesntHave('usuario', function ($query) {
                $query->whereColumn('datos_fiscales_personal', 'datos_fiscales.id');
            })
            ->get();
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

    public function guardarRegimenesFiscales(array $regimenes, DatosFiscal $datosFiscales)
    {
        $idDatoFiscal = $datosFiscales->id;
        $idRegimenPredeterminado = null;

        foreach ($regimenes as $regimen) {
            $idRegimen = $regimen['id_regimen'];
            $esRegimenPredeterminado = $regimen['predeterminado'] ?? false;
            $usosCfdi = $regimen['usos_cfdi'] ?? [];
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

    public function extraerDatosCFDI(Request $request)
    {
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
        }
        $textoExtraido = $this->extraerTextoDePDF($archivo);
        return $textoExtraido;
    }
    private function extraerTextoDePDF($archivo)
    {
        try {
        $texto = (new Pdf())
            ->setPdf($archivo) // Usar el archivo recibido, no 'book.pdf'
            ->text();

            $texto = $this->aiService->extractStructuredData($texto, 'cfdi_extraction');

            return $texto ?: '';
        } catch (\Exception $e) {

            return null;
        }
    }
}
