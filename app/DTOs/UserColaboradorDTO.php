<?php

namespace App\DTOs;

class UserColaboradorDTO
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
        // NUEVO: lista de facturantes permitidos (cada item: id, rfc, nombre_razon, predeterminado)
        public ?array $facturantesPermitidos = null,
    ) {}

    public static function fromUserModel($user): self
    {
        // Obtener datos fiscales principales
        $padre = $user->padre;
        if (!$padre) {
            $padre = $user;
        }

        $tieneDatosFiscalesPersonal = $user->datosFiscalesPersonal !== null;
        $tieneDatosFiscalesPredeterminado = $user->datosFiscalesPrincipal !== null;
        $tieneSuscripcionActiva = $padre->suscripcionActiva !== null;

        // Manejar dirección personal (puede ser null)
        $direccionPersonalArray = null;
        if ($user->direccionPersonal) {
            $direccionPersonalArray = $user->direccionPersonal->toArray();
        }

        if ($padre->suscripcionActiva) {
            $suscripcionArray = [
                'id' => $padre->suscripcionActiva->id,
                'id_plan' => $padre->suscripcionActiva->id_plan,
                'fecha_inicio' => $padre->suscripcionActiva->fecha_inicio,
                'fecha_vencimiento' => $padre->suscripcionActiva?->fecha_vencimiento,
                'estado' => $padre->suscripcionActiva->estado,
                'perfiles_utilizados' => $padre->suscripcionActiva->perfiles_utilizados,
                'facturas_realizadas' => $padre->suscripcionActiva->facturas_realizadas,
                'plan' => $padre->suscripcionActiva->plan ? [
                    'id' => $padre->suscripcionActiva->plan->id,
                    'nombre_plan' => $padre->suscripcionActiva->plan->nombre_plan,
                    'tipo_plan' => $padre->suscripcionActiva->plan->tipo_plan,
                    'tipo_pago' => $padre->suscripcionActiva->plan->tipo_pago,
                    'vigencia_inicio' => $padre->suscripcionActiva->plan->vigencia_inicio?->format('d-m-Y'),
                    'vigencia_fin' => $padre->suscripcionActiva->plan->vigencia_fin?->format('d-m-Y'),
                ] : null,
            ];
        } else {
            $suscripcionArray = [];
        }

        // NUEVO: mapear facturantesPermitidos (asegúrate de eager load con with('facturantesPermitidos'))
        $facturantesArray = null;
        if ($user->relationLoaded('facturantesPermitidos') || $user->facturantesPermitidos) {
            $facturantesArray = collect($user->facturantesPermitidos)->map(function ($f) {
                return [
                    'id' => $f->id,
                    'rfc' => $f->rfc ?? null,
                    'nombre_razon' => $f->nombre_razon ?? null,
                    // campo extra en la pivote
                    'predeterminado' => $f->pivot->predeterminado ?? false,
                ];
            })->toArray();
        }

        return new self(
            id: $user->id,
            nombre: $user->datosFiscalesPersonal?->nombre_razon,
            primer_apellido: $user->datosFiscalesPersonal?->primer_apellido,
            segundo_apellido: $user->datosFiscalesPersonal?->segundo_apellido,
            email: $user->email,
            rol: $user->descripcion_rol,
            id_rol: $user->idRol,
            telefono: $user->phone,
            id_departamento: $user->id_departamento,
            departamento: $user->descripcio_depatamento,
            saldo: (float) $padre->saldo,
            datosCompletos: $tieneDatosFiscalesPersonal,
            direccionPersonal: $direccionPersonalArray,
            tienDatoFiscal: $tieneDatosFiscalesPredeterminado,
            password_temporal: $user->password_temporal,
            id_estatus_usuario: $user->id_estatus_usuario,
            suscripcionActiva: $suscripcionArray,
            tieneSuscripcionActiva: $tieneSuscripcionActiva,
            enviar_correo: $user->datosFiscalesPersonal?->enviar_correo,
            rfcPrincipal: $user->datosFiscalesPrincipal?->rfc,
            razonSocialPrincipal: $user->datosFiscalesPrincipal?->nombre_razon,
            facturantesPermitidos: $facturantesArray,
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
            'enviar_correo' => $this->enviar_correo,
            'rfcPrincipal' => $this->rfcPrincipal,
            'razonSocialPrincipal' => $this->razonSocialPrincipal,
            // NUEVO
            'facturantesPermitidos' => $this->facturantesPermitidos,
        ];
    }
}
