<?php

namespace App\Services;

use App\Mail\MandarCorreo;
use App\Mail\MandarCorreoRecuperacion;
use App\Mail\MandarCorreoConfirmacion;
use App\Mail\MandarCorreoEliminar;
use App\Mail\MandarCorreoEmpleado;
use App\Mail\MandarCorreoFactura;
use App\Mail\MandarCorreoHijo;
use App\Mail\MandarCorreoInhabilitar;
use App\Mail\MandarCorreoValReceptor;
use App\Models\PasswordReset;
use App\Models\PasswordConf;
use App\Models\PasswordEliminar;
use App\Models\PasswordInhabilitar;
use App\Models\SistemaTickets\CatEstatusSolicitud;
use App\Models\SistemaTickets\TabSolicitud;
use App\Models\Solicitud;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class EmailService
{
    public function enviarCorreoSolicitud(array $datos)
    {
        $solicitud = Solicitud::find($datos['ticket']);
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
    public function enviarCorreoFac($email, $archivos)
    {
        try {
            $datosMail = [
                'email' => $email
            ];
            

            Mail::to($email)->send(new MandarCorreoFactura($datosMail, $archivos));
            return "Exito";
        } catch (\Exception $e) {
            // Guardar el error en log, base de datos, o notificar al admin
            Log::error('Error al enviar correo: ' . $e->getMessage());
            return $e->getMessage();
        }
    }
    public function enviarCorreoRec($email)
    {
        $usr = User::whereHas('mailPrincipal', function ($query) use ($email) {
            $query->where('email', $email);
        })->first();
        if ($usr) {
            $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
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
        } else {
            return "null";
        }
    }

    public function enviarCorreoConf($email)
    {
        $usr = User::whereHas('mailPrincipal', function ($query) use ($email) {
            $query->where('email', $email);
        })->first();
        if ($usr) {
            $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
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
        } else {
            return "null";
        }
    }

        public function enviarCorreoValReceptor($id_user,$email)
    {
        $usr = User::find($id_user);
        if ($usr) {
            $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $datosMail = [
                'email' => $email,
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
                Mail::to($datosMail["email"])->send(new MandarCorreoValReceptor($datosMail));
                return "Exito";
            } catch (\Exception $e) {
                // Guardar el error en log, base de datos, o notificar al admin
                Log::error('Error al enviar correo: ' . $e->getMessage());
                return $e->getMessage();
            }
            return $usr;
        } else {
            return "null";
        }
    }

    public function enviarCorreoInhabilitar($email)
    {
        $usr = User::whereHas('mailPrincipal', function ($query) use ($email) {
            $query->where('email', $email);
        })->first();
        if ($usr) {
            $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $datosMail = [
                'email' => $usr->email,
                'nombre' => $usr->name . " " . $usr->apellidoP . " " . $usr->apellidoM,
                'codigo' => $codigo,
            ];
            // Guardar nuevo código
            PasswordInhabilitar::create([
                'email' => $datosMail['email'],
                'codigo' =>  Hash::make($codigo),
                'created_at' => Carbon::now(),
            ]);


            try {
                Mail::to($datosMail["email"])->send(new MandarCorreoInhabilitar($datosMail));
                return "Exito";
            } catch (\Exception $e) {
                // Guardar el error en log, base de datos, o notificar al admin
                Log::error('Error al enviar correo: ' . $e->getMessage());
                return $e->getMessage();
            }
            return $usr;
        } else {
            return "null";
        }
    }
    public function enviarCorreoEliminar($email)
    {
        $usr = User::whereHas('mailPrincipal', function ($query) use ($email) {
            $query->where('email', $email);
        })->first();
        if ($usr) {
            $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $datosMail = [
                'email' => $usr->email,
                'nombre' => $usr->name . " " . $usr->apellidoP . " " . $usr->apellidoM,
                'codigo' => $codigo,
            ];
            // Guardar nuevo código
            PasswordEliminar::create([
                'email' => $datosMail['email'],
                'codigo' =>  Hash::make($codigo),
                'created_at' => Carbon::now(),
            ]);


            try {
                Mail::to($datosMail["email"])->send(new MandarCorreoEliminar($datosMail));
                return "Exito";
            } catch (\Exception $e) {
                // Guardar el error en log, base de datos, o notificar al admin
                Log::error('Error al enviar correo: ' . $e->getMessage());
                return $e->getMessage();
            }
            return $usr;
        } else {
            return "null";
        }
    }
    public function enviarCorreoHijo($email, $password)
    {

        $datosMail = [
            'email' => $email,
            'password_temporal' => $password,
            'url_login' => 'https://tudominio.com/login'
        ];



        try {
            Mail::to($datosMail["email"])->send(new MandarCorreoHijo($datosMail));
            return "Exito";
        } catch (\Exception $e) {
            // Guardar el error en log, base de datos, o notificar al admin
            Log::error('Error al enviar correo: ' . $e->getMessage());
            return $e->getMessage();
        }
        return $usr;
    }
    public function enviarCorreoEmpleado($email, $password)
    {

        $datosMail = [
            'email' => $email,
            'password_temporal' => $password,
            'url_login' => 'https://tudominio.com/login'
        ];



        try {
            Mail::to($datosMail["email"])->send(new MandarCorreoEmpleado($datosMail));
            return "Exito";
        } catch (\Exception $e) {
            // Guardar el error en log, base de datos, o notificar al admin
            Log::error('Error al enviar correo: ' . $e->getMessage());
            return $e->getMessage();
        }
        return $usr;
    }

    // Puedes agregar más métodos según el tipo de correo
}
