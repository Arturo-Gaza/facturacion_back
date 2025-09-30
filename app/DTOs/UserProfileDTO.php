<?php

namespace App\DTOs;

class UserProfileDTO
{
    public function __construct(
        public int $id,
        public ?string $nombre,
        public ?string $primer_apellido,
        public ?string $segundo_apellido,
        public ?string $email,
        public ?string $rol,
        public ?int $id_rol,
        public ?string $telefono,
        public ?int $id_departamento,
        public ?string $departamento,
        public float $saldo,
        public ?bool $datosCompletos,
        public ?bool $tienDatoFiscal,
        public ?array $direccionPersonal,
    ) {}

    public static function fromUserModel($user): self
    {
        // Obtener datos fiscales principales

        $tieneDatosFiscalesPersonal = $user->datosFiscalesPersonal !== null;
         $tieneDatosFiscalesPredeterminado = $user->datosFiscalesPrincipal !== null;
        return new self(
            id: $user->id,
            nombre: $user->datosFiscalesPersonal?->nombre_razon, // Nullsafe operator
            primer_apellido: $user->datosFiscalesPersonal?->primer_apellido,
            segundo_apellido: $user->datosFiscalesPersonal?->segundo_apellido,
            email: $user->email,
            rol: $user->descripcion_rol,
            id_rol: $user->idRol,
            telefono: $user->phone,
            id_departamento: $user->id_departamento,
            departamento: $user->descripcio_depatamento,
            saldo: (float) $user->saldo,
            datosCompletos: $tieneDatosFiscalesPersonal,
            direccionPersonal:$user->direccionPersonal->toArray() ,
            tienDatoFiscal:$tieneDatosFiscalesPredeterminado
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'primer_apellido' => $this->primer_apellido,
            'segundo_apellido' => $this->segundo_apellido,
            'email' => $this->email,
            'rol' => $this->rol,
            'id_rol' => $this->id_rol,
            'telefono' => $this->telefono,
            'id_departamento' => $this->id_departamento,
            'departamento' => $this->departamento,
            'saldo' => $this->saldo,
            'datosCompletos' => $this->datosCompletos,
            'direccionPersonal' => $this->direccionPersonal,
            'tienDatoFiscal'=>$this->tienDatoFiscal
        ];
    }
}
