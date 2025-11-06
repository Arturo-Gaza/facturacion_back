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
        public ?bool $password_temporal,
        public ?int $id_estatus_usuario,
        public ?array $suscripcionActiva,
        public ?bool $tieneSuscripcionActiva,
        public ?bool $enviar_correo,
        public ?string $rfcPrincipal,
        public ?string $razonSocialPrincipal,
    ) {}

    public static function fromUserModel($user): self
    {
        // Obtener datos fiscales principales

        $tieneDatosFiscalesPersonal = $user->datosFiscalesPersonal !== null;
        $tieneDatosFiscalesPredeterminado = $user->datosFiscalesPrincipal !== null;
        $tieneSuscripcionActiva = $user->suscripcionActiva !== null;
        // Manejar dirección personal (puede ser null)
        $direccionPersonalArray = null;
        if ($user->direccionPersonal) {
            $direccionPersonalArray = $user->direccionPersonal->toArray();
        }
        if ($user->suscripcionActiva) {

            $suscripcionArray = [
                'id' => $user->suscripcionActiva->id,
                'id_plan' => $user->suscripcionActiva->id_plan,
                'fecha_inicio' => $user->suscripcionActiva->fecha_inicio->format('Y-m-d'),
                'fecha_vencimiento' => $user->suscripcionActiva?->fecha_vencimiento?->format('Y-m-d'),
                'estado' => $user->suscripcionActiva->estado,
                'perfiles_utilizados' => $user->suscripcionActiva->perfiles_utilizados,
                'facturas_realizadas' => $user->suscripcionActiva->facturas_realizadas,
                'plan' => $user->suscripcionActiva->plan ? [
                    'id' => $user->suscripcionActiva->plan->id,
                    'nombre_plan' => $user->suscripcionActiva->plan->nombre_plan,
                    'tipo_plan' => $user->suscripcionActiva->plan->tipo_plan,
                    'tipo_pago' => $user->suscripcionActiva->plan->tipo_pago,
                    'vigencia_inicio' => $user->suscripcionActiva->plan->vigencia_inicio?->format('Y-m-d'),
                    'vigencia_fin' => $user->suscripcionActiva->plan->vigencia_fin?->format('Y-m-d'),
                ] : null
            ];
        } else {
            $suscripcionArray = [];
        }

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
            direccionPersonal: $direccionPersonalArray,
            tienDatoFiscal: $tieneDatosFiscalesPredeterminado,
            password_temporal: $user->password_temporal,
            id_estatus_usuario: $user->id_estatus_usuario,
            suscripcionActiva: $suscripcionArray,
            tieneSuscripcionActiva: $tieneSuscripcionActiva,
            enviar_correo: $user->datosFiscalesPersonal?->enviar_correo,
            rfcPrincipal: $user->datosFiscalesPrincipal?->rfc ,
            razonSocialPrincipal: $user->datosFiscalesPrincipal?->nombre_razon ,

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
            'tienDatoFiscal' => $this->tienDatoFiscal,
            'password_temporal' => $this->password_temporal,
            'id_estatus_usuario' => $this->id_estatus_usuario,
            'suscripcionActiva' => $this->suscripcionActiva,
            'tieneSuscripcionActiva' => $this->tieneSuscripcionActiva,
            'enviar_correo' => $this->enviar_correo
        ];
    }
}
