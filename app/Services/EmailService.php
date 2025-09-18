<?php

namespace App\Services;

use App\Mail\MandarCorreo;
use App\Mail\MandarCorreoRecuperacion;
use App\Mail\MandarCorreoConfirmacion;
use App\Models\PasswordReset;
use App\Models\PasswordConf;
use App\Models\SistemaTickets\CatEstatusSolicitud;
use App\Models\SistemaTickets\TabSolicitud;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class EmailService
{
    public function enviarCorreoSolicitud(array $datos)
    {
        $solicitud = TabSolicitud::find($datos['ticket']);
        $usrSol = User::find($solicitud->id_usuario_solicitud);
        $usrComp = User::find($solicitud->id_usuario_asignacion);
        $esatus = CatEstatusSolicitud::find($datos['id_estatus']);
        if ($solicitud) {
            $datosSol = [

                'ticket' => $solicitud->id,
                'prioridad' => $solicitud->prioridad_valor,
                'departamento' => $solicitud->descripcion_departamento,
                'estatus' => $esatus->descripcion_estatus_solicitud
            ];
        }

        if ($usrSol && $solicitud) {
            $datosUsrSol = [
                'nombre' => $usrSol->name . " " . $usrSol->apellidoP . " " . $usrSol->apellidoM,
                'email' => $usrSol->email,

            ];
            try {
                Mail::to($datosUsrSol["email"])->send(new MandarCorreo($datosUsrSol, $datosSol));
            } catch (\Exception $e) {
                // Guardar el error en log, base de datos, o notificar al admin
                Log::error('Error al enviar correo: ' . $e->getMessage());
            }
        }
        if ($usrComp && $solicitud) {
            $datosUsrComp = [
                'nombre' => $usrComp->name . " " . $usrComp->apellidoP . " " . $usrComp->apellidoM,
                'email' => $usrComp->email,

            ];
            try {
                Mail::to($datosUsrComp["email"])->send(new MandarCorreo($datosUsrComp, $datosSol));
            } catch (\Exception $e) {
                // Guardar el error en log, base de datos, o notificar al admin
                Log::error('Error al enviar correo: ' . $e->getMessage());
            }
        }
    }

    public function enviarCorreoRec($email)
    {
        $usr = User::whereHas('mailPrincipal', function ($query) use ($email) {
    $query->where('email', $email);
})->first();
        if ($usr) {
            $codigo= str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $datosMail = [
                'email' => $usr->email,
                'nombre' => $usr->name . " " . $usr->apellidoP . " " . $usr->apellidoM,
                'codigo' => $codigo,
            ];



            // Guardar nuevo código
            PasswordReset::create([
                'email' => $datosMail['email'],
                'codigo' =>  Hash::make($codigo),
                'created_at' => Carbon::now(),
            ]);


            try {
                Mail::to($datosMail["email"])->send(new MandarCorreoRecuperacion($datosMail));
                return "Exito";
            } catch (\Exception $e) {
                // Guardar el error en log, base de datos, o notificar al admin
                Log::error('Error al enviar correo: ' . $e->getMessage());
                return $e->getMessage();
            }
            return $usr;
        }else{
            return "null";
        }
    }

    public function enviarCorreoConf($email)
    {
        $usr = User::whereHas('mailPrincipal', function ($query) use ($email) {
    $query->where('email', $email);
})->first();
        if ($usr) {
            $codigo= str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $datosMail = [
                'email' => $usr->email,
                'nombre' => $usr->name . " " . $usr->apellidoP . " " . $usr->apellidoM,
                'codigo' => $codigo,
            ];



            // Guardar nuevo código
            PasswordConf::create([
                'email' => $datosMail['email'],
                'codigo' =>  Hash::make($codigo),
                'created_at' => Carbon::now(),
            ]);


            try {
                Mail::to($datosMail["email"])->send(new MandarCorreoConfirmacion($datosMail));
                return "Exito";
            } catch (\Exception $e) {
                // Guardar el error en log, base de datos, o notificar al admin
                Log::error('Error al enviar correo: ' . $e->getMessage());
                return $e->getMessage();
            }
            return $usr;
        }else{
            return "null";
        }
    }


    // Puedes agregar más métodos según el tipo de correo
}
