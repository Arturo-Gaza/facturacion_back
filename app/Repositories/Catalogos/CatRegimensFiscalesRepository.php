<?php

namespace App\Repositories\Catalogos;

use App\Interfaces\Catalogos\CatRegimenesFiscaslesRepositoryInterface;
use App\Models\Catalogos\CatRegimenesFiscales;
use Illuminate\Support\Facades\DB;
use LDAP\Result;

class CatRegimensFiscalesRepository implements CatRegimenesFiscaslesRepositoryInterface
{
    public function getAll()
    {
        return CatRegimenesFiscales::all();
    }

    public function getByID($id): ?CatRegimenesFiscales
    {
        return CatRegimenesFiscales::where('id_regimen', $id)->first();
    }
    public function getByMoralOFisica($esPersonaMoral)
    {


 $tipoPersona = $esPersonaMoral ? 'aplica_persona_moral' : 'aplica_persona_fisica';
    
    return DB::table('cat_regimenes_fiscales as rf')
        ->select(
            'rf.id_regimen',
            'rf.clave',
            'rf.descripcion as descripcion_regimen',
            'rf.aplica_persona_fisica',
            'rf.aplica_persona_moral',
            'uc.usoCFDI',
            'uc.descripcion as descripcion_uso_cfdi',
            'uc.aplica_persona_fisica as uso_aplica_fisica',
            'uc.aplica_persona_moral as uso_aplica_moral'
        )
        ->join('regimen_uso_cfdi as ruc', 'rf.id_regimen', '=', 'ruc.id_regimen')
        ->join('cat_usos_cfdi as uc', 'ruc.usoCFDI', '=', 'uc.usoCFDI')
        ->where('rf.' . $tipoPersona, true)
        ->where('uc.' . $tipoPersona, true)
        ->whereNull('rf.fecha_fin_vigencia')
        ->whereNull('uc.fecha_fin_vigencia')
        ->orderBy('rf.clave')
        ->orderBy('uc.usoCFDI')
        ->get();
    }

    public function store(array $data): CatRegimenesFiscales
    {
        return CatRegimenesFiscales::create($data);
    }

    public function update(array $data, $id): ?CatRegimenesFiscales
    {
        $regimen = CatRegimenesFiscales::where('id_regimen', $id)->first();
        if ($regimen) {
            $regimen->update($data);
        }
        return $regimen;
    }
}
