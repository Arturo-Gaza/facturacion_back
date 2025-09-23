<?php

namespace App\Repositories;

use App\DTOs\UserProfileDTO;
use App\Interfaces\DatosFiscalesRepositoryInterface;
use App\Models\DatosFiscal;
use App\Models\Direccion;
use App\Models\User;
use App\Models\UsuarioRegimenFiscal;

class DatosFiscalesRepository implements DatosFiscalesRepositoryInterface
{
    public function getAll()
    {
        return DatosFiscal::with('direcciones')->get();
    }

    public function getByID($id): ?DatosFiscal
    {
        return DatosFiscal::with('direcciones')->find($id);
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
            'datos_fiscales_principal' => $datosFiscales->id
        ]);
        // Recargar el usuario con las relaciones actualizadas
        $user->load(['datosFiscalesPrincipal', 'rol', 'departamento', 'mailPrincipal', 'telefonoPrincipal']);

        // Devolver el DTO
        return UserProfileDTO::fromUserModel($user);
    }

    public function storeCompleto(array $data, array $direccion, array $regimenes)
    {
        $datosFiscales = DatosFiscal::create($data);
        $this->guardarRegimenesFiscales($data['id_usuario'], $regimenes, $datosFiscales);

        if ($direccion && $datosFiscales) {
            $direccion['id_fiscal'] = $datosFiscales->id;
            $direccion['id_tipo_direccion'] = 1;
            Direccion::create($direccion);
        }
        // Actualizar el usuario con los nuevos datos fiscales principales
        $user = User::Find($datosFiscales->id_usuario);

        // Devolver el DTO
        return UserProfileDTO::fromUserModel($user);
    }

    protected function guardarRegimenesFiscales($userId, array $regimenes, DatosFiscal $datosFiscales)
    {
        $regimenesGuardados = [];

        foreach ($regimenes as $regimenData) {
            $regimen = UsuarioRegimenFiscal::create([
                'id_usuario' => $userId,
                'id_regimen' => $regimenData['id_regimen'],
                'predeterminado' => $regimenData['predeterminado'] ?? false
            ]);

            $regimenesGuardados[] = $regimen;

            // Si es predeterminado, actualizar la referencia en datos_fiscales
            if ($regimen->predeterminado) {
                $datosFiscales->update(['id_regimen_predeterminado' => $regimen->id]);
            }
        }

        // Si ningún régimen se marcó como predeterminado, marcar el primero
        if (!$datosFiscales->id_regimen_predeterminado && count($regimenesGuardados) > 0) {
            $primerRegimen = $regimenesGuardados[0];
            $primerRegimen->update(['predeterminado' => true]);
            $datosFiscales->update(['id_regimen_predeterminado' => $primerRegimen->id]);
        }

        return $regimenesGuardados;
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
}
