<?php

namespace App\Repositories\Usuario;

use App\DTOs\UserProfileDTO;
use App\Interfaces\Usuario\UsuarioRepositoryInterface;
use App\Models\AsignacionCarga\tab_asignacion;
use App\Models\DatosFiscal;
use App\Models\PasswordReset;
use App\Models\PasswordConf;
use App\Models\PasswordEliminar;
use App\Models\PasswordInhabilitar;
use App\Models\User;
use App\Models\UserSistema;
use App\Models\UserEmail;

use App\Models\UserPhone;
use App\Models\UsuarioRol;
use App\Services\EmailService;
use App\Services\TwilioService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioRepository implements UsuarioRepositoryInterface
{
    protected $emailService;
    protected $twilioService;

    public function __construct(EmailService $emailService, TwilioService $twilioService)
    {
        $this->emailService = $emailService;
        $this->twilioService = $twilioService;
    }


    public function getAll()
    {
        $usuario = User::all();

        return $usuario;
    }

    public function getMesaAyuda()
    {
        return User::join('cat_roles', 'users.idRol', '=', 'cat_roles.id')
            ->where('cat_roles.consola', true)
            ->select('users.*')
            ->get();
    }

    public function getAllUserAlmacen($idCarga)
    {
        $usuario = User::select(
            'id',
            'user',
            'name',
            'apellidoP',
            'apellidoM',
            'email',
            'idRol',
            'habilitado',
        )
            ->where('idRol', 2)->get();

        $data1 = array();
        foreach ($usuario as $val) {
            $data1[] = $val;
        }

        $usuarioAsigndo = User::select(
            'users.id',
            'users.user',
            'users.name',
            'users.apellidoP',
            'users.apellidoM',
            'users.email',
            'users.idRol',
            'users.habilitado',
            'tab_asignacions.habilitado AS asigHabilitado',
        )
            ->join('tab_asignacions', 'tab_asignacions.id_usuario', '=', 'users.id')
            ->orWhere('tab_asignacions.id_carga', '=', $idCarga)
            ->groupBy('users.id')
            ->groupBy('tab_asignacions.habilitado')
            ->get()->filter(function ($user) {
                return $user->asigHabilitado == 1 && $user->idRol == 2;
            });

        $data2 = array();
        foreach ($usuarioAsigndo as $val) {
            $data2[] = $val;
        }

        $idsData2 = array_column($data2, 'id');
        $resultadoArr = array_diff($data1, array_filter($data1, function ($item) use ($idsData2) {
            return in_array($item['id'], $idsData2);
        }));

        $results = array();
        foreach ($resultadoArr as $val) {
            $results[] = $val;
        }
        return $results;

        //POR SI SE OCUPA EN OTRO LADO
        // $usuario = User::select(
        //     'users.id',
        //     'users.user',
        //     'users.name',
        //     'users.apellidoP',
        //     'users.apellidoM',
        //     'users.email',
        //     'users.idRol',
        //     'users.habilitado',
        //     'tab_asignacions.habilitado AS habilitadoTabAsig',
        //     'tab_asignacions.id_carga'
        // )
        //     ->leftJoin('tab_asignacions', 'tab_asignacions.id_usuario', '=', 'users.id')
        //     ->where(function ($query) use ($idCarga) {
        //         $query->where('tab_asignacions.id_usuario', null)
        //             ->orWhere('tab_asignacions.habilitado', 0)
        //             ->orWhere('tab_asignacions.id_carga', '!=', $idCarga);
        //     })
        //     ->groupBy('users.id')
        //     ->groupBy('tab_asignacions.habilitado')
        //     ->groupBy('tab_asignacions.id_carga')
        //     ->get()->filter(function ($user) {
        //         return $user->idRol == 2;
        //     });

        // $results = array();
        // foreach ($usuario as $val) {
        //     $results[] = $val;
        // }

        // return $results;
    }

    public function getAllUser()
    {
        $usuario = User::select(
            'id',
            'user',
            'name',
            'apellidoP',
            'apellidoM',
            'email',
            'idRol',
            'habilitado',
        )
            ->where('idRol', 2)->get();

        $data1 = array();
        foreach ($usuario as $val) {
            $data1[] = $val;
        }


        return $data1;
    }

    public function getColaboradores($id)
    {
        $users = User::with(['datosFiscalesPrincipal', 'rol', 'departamento', 'mailPrincipal', 'telefonoPrincipal'])
            ->where('usuario_padre', $id)
            ->whereNot('id_estatus_usuario', 3)
            ->get();
        // Crear array de DTOs
        $dtos = $users->map(function ($user) use ($id) {
            return UserProfileDTO::fromUserModel($user);
        })->toArray();

        // Devolver el array de DTOs
        return $dtos;
    }

    public function getAllUserAsignado($idCarga)
    {
        $usuario = User::select(
            'users.id',
            'users.user',
            'users.name',
            'users.apellidoP',
            'users.apellidoM',
            'users.email',
            'users.idRol',
            'users.habilitado',
            'tab_asignacions.habilitado AS asigHabilitado',
            'tab_asignacions.id_estatus'
        )
            ->join('tab_asignacions', 'tab_asignacions.id_usuario', '=', 'users.id')
            ->orWhere('tab_asignacions.id_carga', '=', $idCarga)
            ->groupBy('users.id')
            ->groupBy('tab_asignacions.habilitado')
            ->groupBy('tab_asignacions.id_estatus')
            ->get()->filter(function ($user) {
                return $user->asigHabilitado == 1 && $user->idRol == 2;
            });

        $results = array();
        foreach ($usuario as $val) {
            $results[] = $val;
        }

        return $results;
    }


    public function getByID($id): ?User
    {
        return User::where('id', $id)->first();
    }
    public function getDatos($id): ?UserProfileDTO
    {
        $usr = User::where('id', $id)->first();
        return UserProfileDTO::fromUserModel($usr);
    }

    public function editarDatos($request, $id)
    {
        DB::beginTransaction();
        $user = User::with(['datosFiscalesPersonal.domicilioPersonal'])
            ->where('id', $id)
            ->firstOrFail();
        $datosFiscales = $user->datosFiscalesPersonal;
        $direccionActual = $datosFiscales->domicilioPersonal;
        $datosFiscalesData = [
            'nombre_razon' => $request->nombre ?? null,
            'primer_apellido' => $request->primer_apellido ?? null,
            'segundo_apellido' => $request->segundo_apellido ?? null,
            'segundo_apellido' => $request->segundo_apellido ?? null,
            'enviar_correo' => $request->enviar_correo ?? null
        ];
        $direccon = $request->direccionPersonal;
        $direccionDataCompleta = [
            'calle' => $direccon["calle"] ?? null,
            'num_exterior' => $direccon["num_exterior"] ?? null,
            'num_interior' => $direccon["num_interior"] ?? null,
            'colonia' => $direccon["colonia"] ?? null,
            'localidad' => $direccon["localidad"] ?? null,
            'municipio' => $direccon["municipio"] ?? null,
            'estado' => $direccon["estado"] ?? null,
            'codigo_postal' => $direccon["codigo_postal"] ?? null
        ];
        if ($datosFiscales) {
            $datosFiscales->update($datosFiscalesData);
        }
        if ($direccionActual) {
            $direccionActual->update($direccionDataCompleta);
        }

        DB::commit();
        $user = User::where('id', $id)
            ->firstOrFail();;
        $user->load(['datosFiscalesPrincipal', 'rol', 'departamento', 'mailPrincipal', 'telefonoPrincipal']);

        return UserProfileDTO::fromUserModel($user);
    }

    public function getAllHabilitados()
    {
        return User::where('habilitado', 1)->get();
    }
    public function enviarCorreoRec($data)
    {
        $usr = $this->findByEmailOrUser($data['email']);
        if (!$usr)
            return null;
        $email = $usr->email;

        $usr = $this->emailService->enviarCorreoRec($email);
        return $usr;
    }

    public function enviarCorreoConf($data)
    {
        $usr = $this->findByEmailOrUser($data['email']);
        if (!$usr)
            return null;
        $email = $usr->email;

        $usr = $this->emailService->enviarCorreoConf($email);
        return $usr;
    }

    public function enviarCorreoInhabilitar($data)
    {
        $usr = $this->findByEmailOrUser($data['email']);
        if (!$usr)
            return null;
        $email = $usr->email;

        $usr = $this->emailService->enviarCorreoInhabilitar($email);
        return $usr;
    }

    public function enviarCorreoEliminar($data)
    {
        $usr = $this->findByEmailOrUser($data['email']);
        if (!$usr)
            return null;
        $email = $usr->email;

        $usr = $this->emailService->enviarCorreoEliminar($email);
        return $usr;
    }

    public function enviarSMSConf($data)
    {
        $usr = $this->findByEmailOrUser($data['phone']);
        if (!$usr)
            return null;
        $phone = $usr->phone;

        $usr = $this->twilioService->sendSMSConf($phone);
        return $usr;
    }

    public function recPass($data)
    {
        $codigo = $data['codigo'];
        $usr = $this->findByEmailOrUser($data['email']);
        if (!$usr)
            return null;
        $email = $usr->email;
        $nuevaPass = Hash::make($data['nuevaPass']);

        $passwordReset = PasswordReset::where('email', $email)
            ->where('used', false)
            ->get();

        foreach ($passwordReset as $reset) {
            if (Hash::check($codigo, $reset->codigo)) {
                // Verificar si el código ha expirado


                // Si quieres marcarlo como usado aquí
                $reset->used = true;
                $reset->used_at = now();
                $reset->save();

                //  $usr = User::where('email', $email)->first();
                $usr->password = $nuevaPass;
                $usr->save();
                return "Contraseña cambiada con exito";
            }
        }


        return "Error inesperado";
    }

    public function desHabilitar($data)
    {
        $codigo = $data['codigo'];
        $usr = $this->findByEmailOrUser($data['email']);
        if (!$usr)
            return null;
        $email = $usr->email;

        $passwordReset = PasswordInhabilitar::where('email', $email)
            ->where('used', false)
            ->get();

        foreach ($passwordReset as $reset) {
            if (Hash::check($codigo, $reset->codigo)) {
                // Verificar si el código ha expirado


                // Si quieres marcarlo como usado aquí
                $reset->used = true;
                $reset->used_at = now();
                $reset->save();

                //  $usr = User::where('email', $email)->first();
                $usr->id_estatus_usuario = 2;
                $usr->save();
                return "Usuario inhabilitado correctamente";
            }
        }


        throw new Exception('Error inesperado', 409);
    }
    public function eliminar($data)
    {
        $codigo = $data['codigo'];
        $usr = $this->findByEmailOrUser($data['email']);
        if (!$usr)
            return null;
        $email = $usr->email;

        $passwordReset = PasswordEliminar::where('email', $email)
            ->where('used', false)
            ->get();

        foreach ($passwordReset as $reset) {
            if (Hash::check($codigo, $reset->codigo)) {
                // Verificar si el código ha expirado
                // Si quieres marcarlo como usado aquí
                $reset->used = true;
                $reset->used_at = now();
                $reset->save();

                //  $usr = User::where('email', $email)->first();
                $usr->id_estatus_usuario = 3;
                $usr->save();
                return "Usuario eliminado correctamente";
            }
        }


        throw new Exception('Error inesperado', 409);
    }
    public function desHabilitarPorAdmin($data)
    {
        $email_padre = $data['email_padre'];
        $email_hijo = $data['email_hijo'];
        $usrPadre = $this->findByEmailOrUser($email_padre);
        $usrHijo = $this->findByEmailOrUser($email_hijo);

        if (!$usrPadre) {
            throw new Exception('Usuario padre no encontrado', 404);
        }

        if (!$usrHijo) {
            throw new Exception('Usuario hijo no encontrado', 404);
        }
        if ($usrHijo->usuario_padre == $usrPadre->id) {
            // Verificar si el código ha expirado

            //  $usr = User::where('email', $email)->first();
            $usrHijo->id_estatus_usuario = 2;
            $usrHijo->save();
            return "Usuario inhabilitado correctamente";
        }
        throw new Exception('Error inesperado', 409);
    }
    public function eliminarPorAdmin($data)
    {
        $email_padre = $data['email_padre'];
        $email_hijo = $data['email_hijo'];
        $usrPadre = $this->findByEmailOrUser($email_padre);
        $usrHijo = $this->findByEmailOrUser($email_hijo);

        if (!$usrPadre) {
            throw new Exception('Usuario padre no encontrado', 404);
        }

        if (!$usrHijo) {
            throw new Exception('Usuario hijo no encontrado', 404);
        }
        if ($usrHijo->usuario_padre == $usrPadre->id) {
            // Verificar si el código ha expirado

            //  $usr = User::where('email', $email)->first();
            $usrHijo->id_estatus_usuario = 3;
            $usrHijo->save();
            return "Usuario inhabilitado correctamente";
        }
        throw new Exception('Error inesperado', 409);
    }


    public function validarCorreoRec($data)
    {
        $codigo = $data['codigo'];
        $usr = $this->findByEmailOrUser($data['email']);
        if (!$usr)
            return null;
        $email = $usr->email;
        $expiraEnMinutos = 10;
        $passwordReset = PasswordReset::where('email', $email)
            ->where('used', false)
            ->get();

        foreach ($passwordReset as $reset) {
            if (Hash::check($codigo, $reset->codigo)) {
                // Verificar si el código ha expirado
                if (Carbon::parse($reset->created_at)->addMinutes($expiraEnMinutos)->isPast()) {
                    return null;
                }


                return "Código válido";
            }
        }
        // Continuar con el flujo para permitir cambiar contraseña
    }
    public function enviarCorreoValReceptor($data)
    {
        $email = $data['email'];
        $id_user = $data['id_user'];
        if (!$email) {
            return null;
        }
        $usr = $this->emailService->enviarCorreoValReceptor($id_user, $email);
        return $usr;
    }

    public function enviarCorreoCambiarCorreo($data)
    {
        $email = $data['email'];
        $id_user = $data['id_user'];
        if (!$email) {
            return null;
        }
        $usr = $this->emailService->enviarCorreoCambiarCorreo($id_user, $email);
        return $usr;
    }
    public function validarCorreoValReceptor($data)
    {
        DB::beginTransaction();
        $codigo = $data['codigo'];

        $email = $data['email'];
        $expiraEnMinutos = 10;
        $passwordReset = PasswordConf::where('email', $email)
            ->where('used', false)
            ->get();

        foreach ($passwordReset as $reset) {
            if (Hash::check($codigo, $reset->codigo)) {
                // Verificar si el código ha expirado
                if (Carbon::parse($reset->created_at)->addMinutes($expiraEnMinutos)->isPast()) {
                    DB::rollBack();
                    return null;
                }
                // Actualizar el registro en password_confirm_mail_tokens
                $reset->update([
                    'used' => true,
                    'used_at' => now()
                ]);

                DB::commit();

                return "Código válido";
            }
        }
        DB::rollBack();
        return null;
    }

    public function validarCorreoCambiarCorreo($data)
    {
        DB::beginTransaction();
        $codigo = $data['codigo'];
        $id_user=$data['id_user'];
        $email = $data['email'];
        $expiraEnMinutos = 10;
        $passwordReset = PasswordConf::where('email', $email)
            ->where('used', false)
            ->get();

        foreach ($passwordReset as $reset) {
            if (Hash::check($codigo, $reset->codigo)) {
                // Verificar si el código ha expirado
                if (Carbon::parse($reset->created_at)->addMinutes($expiraEnMinutos)->isPast()) {
                    DB::rollBack();
                    return null;
                }
                // Actualizar el registro en password_confirm_mail_tokens
                $reset->update([
                    'used' => true,
                    'used_at' => now()
                ]);
                $correoExistente=UserEmail::where("email",$email)->first();
                if($correoExistente){
                    throw new Exception("El correo ya esta registrado en el sistema");
                }
                $user=User::find($id_user);
                $mail=$user->mailPrincipal;
                $mail->email=$email;
                $mail->save();
                DB::commit();

                return "Código válido";
            }
        }
        DB::rollBack();
        return null;
    }

    public function validarCorreoConf($data)
    {
        DB::beginTransaction();
        $codigo = $data['codigo'];
        $usr = $this->findByEmailOrUser($data['email']);
        if (!$usr) {
            DB::rollBack();
            return null;
        }
        $email = $usr->email;
        $expiraEnMinutos = 10;
        $passwordReset = PasswordConf::where('email', $email)
            ->where('used', false)
            ->get();

        foreach ($passwordReset as $reset) {
            if (Hash::check($codigo, $reset->codigo)) {
                // Verificar si el código ha expirado
                if (Carbon::parse($reset->created_at)->addMinutes($expiraEnMinutos)->isPast()) {
                    DB::rollBack();
                    return null;
                }
                // Actualizar el registro en password_confirm_mail_tokens
                $reset->update([
                    'used' => true,
                    'used_at' => now()
                ]);

                // Actualizar el correo como verificado en user_emails
                UserEmail::where('email', $email)
                    ->update([
                        'verificado' => true
                    ]);

                DB::commit();

                return "Código válido";
            }
        }
        DB::rollBack();
        return null;
    }

    public function validarCorreoInhabilitar($data)
    {
        DB::beginTransaction();
        $codigo = $data['codigo'];
        $usr = $this->findByEmailOrUser($data['email']);
        if (!$usr) {
            DB::rollBack();
            return null;
        }
        $email = $usr->email;
        $expiraEnMinutos = 10;
        $passwordReset = PasswordInhabilitar::where('email', $email)
            ->where('used', false)
            ->get();

        foreach ($passwordReset as $reset) {
            if (Hash::check($codigo, $reset->codigo)) {
                // Verificar si el código ha expirado
                if (Carbon::parse($reset->created_at)->addMinutes($expiraEnMinutos)->isPast()) {
                    DB::rollBack();
                    return null;
                }

                DB::commit();

                return "Código válido";
            }
        }
        DB::rollBack();
        return null;
    }
    public function validarCorreoEliminar($data)
    {
        DB::beginTransaction();
        $codigo = $data['codigo'];
        $usr = $this->findByEmailOrUser($data['email']);
        if (!$usr) {
            DB::rollBack();
            return null;
        }
        $email = $usr->email;
        $expiraEnMinutos = 10;
        $passwordReset = PasswordEliminar::where('email', $email)
            ->where('used', false)
            ->get();

        foreach ($passwordReset as $reset) {
            if (Hash::check($codigo, $reset->codigo)) {
                // Verificar si el código ha expirado
                if (Carbon::parse($reset->created_at)->addMinutes($expiraEnMinutos)->isPast()) {
                    DB::rollBack();
                    return null;
                }

                DB::commit();

                return "Código válido";
            }
        }
        DB::rollBack();
        return null;
    }

    public function findByEmailOrUser(string $email): ?User
    {
        return User::where('id_estatus_usuario', 1)
            ->where(function ($query) use ($email) {
                $query->whereHas('mailPrincipal', function ($q) use ($email) {
                    $q->where('email', $email);
                })->orWhereHas('telefonoPrincipal', function ($q) use ($email) {
                    $q->where('telefono', $email);
                });
            })
            ->first();
    }

    public function responseUser(string $email)
    {
        return User::whereHas('mailPrincipal', function ($query) use ($email) {
            $query->where('email', $email);
        })
            ->first();
    }

    public function store(array $data)
    {
        $email = $data['email'];
        $idRol   = $data['idRol'];
        $datos_fiscales_personal = $data["datos_personal"];
        // 1. Buscar email existente
        $existingEmail = UserEmail::where('email', $email)->first();
        if ($existingEmail) {
            if ($existingEmail->verificado) {
                throw new \Exception('Usuario existente ', 409);
            } else {
                // Eliminar usuario completo (esto eliminará en cascada emails y teléfonos)
                $userToDelete = User::where('id_mail_principal', $existingEmail->id)
                    ->orWhereHas('emails', function ($query) use ($email) {
                        $query->where('email', $email);
                    })
                    ->first();

                if ($userToDelete) {
                    $userToDelete->delete(); // Esto eliminará todo en cascada
                } else {
                    $existingEmail->delete();
                }
            }
        }

        $password = $this->generarPasswordAvanzado();
        $usr = $this->emailService->enviarCorreoEmpleado($email, $password);

        // 3. Crear nuevo usuario
        $passwordHash = Hash::make($password);
        $user = User::create([
            'password'  => $passwordHash,
            'idRol'     =>  $idRol,
            'password_temporal' => true
        ]);

        // 4. Guardar correo y teléfono
        $correo = $user->emails()->create([
            'email' => $email,
        ]);
        $datos = DatosFiscal::create([
            'nombre_razon' => $datos_fiscales_personal['nombre_razon'],
            'primer_apellido' => $datos_fiscales_personal['primer_apellido'],
            'segundo_apellido' => $datos_fiscales_personal['segundo_apellido'],
            'id_usuario' => $user->id

        ]);

        $user->update([
            'id_mail_principal' => $correo->id,
            'datos_fiscales_personal' => $datos->id
        ]);

        return $user;
    }

    public function storeCliente(array $data)
    {
        $email = $data['email'];
        $tel   = $data['tel'];

        // 1. Buscar email existente
        $existingEmail = UserEmail::where('email', $email)->first();
        if ($existingEmail) {
            if ($existingEmail->verificado) {
                throw new \Exception('Correo existente ', 409);
            } else {
                // Eliminar usuario completo (esto eliminará en cascada emails y teléfonos)
                $userToDelete = User::where('id_mail_principal', $existingEmail->id)
                    ->orWhereHas('emails', function ($query) use ($email) {
                        $query->where('email', $email);
                    })
                    ->first();

                if ($userToDelete) {
                    $userToDelete->delete(); // Esto eliminará todo en cascada
                } else {
                    $existingEmail->delete();
                }
            }
        }

        // 2. Buscar teléfono existente
        $existingPhone = UserPhone::where('telefono', $tel)->first();
        if ($existingPhone) {
            if ($existingPhone->verificado) {
                throw new \Exception('Telefono existente ', 409);
            } else {
                // Eliminar usuario completo
                $userToDelete = User::where('id_telefono_principal', $existingPhone->id)
                    ->orWhereHas('phones', function ($query) use ($tel) {
                        $query->where('telefono', $tel);
                    })
                    ->first();

                if ($userToDelete) {
                    $userToDelete->delete(); // Esto eliminará todo en cascada
                } else {
                    $existingPhone->delete();
                }
            }
        }

        // 3. Crear nuevo usuario
        $data['password'] = Hash::make($data['password']);
        $user = User::create([
            'password'  => $data['password'],
            'idRol'     => $data['idRol'] ?? 2,
        ]);

        // 4. Guardar correo y teléfono
        $correo = $user->emails()->create([
            'email' => $email,
        ]);

        $telefono = $user->phones()->create([
            'telefono' => $tel,
        ]);

        $user->update([
            'id_mail_principal' => $correo->id,
            'id_telefono_principal' => $telefono->id
        ]);

        return   $user;
    }

    public function storeHijo(array $data)
    {
        $email = $data['email'];
        $idPadre   = $data['id_usuario'];
        $facturantes = $data['facturantes'] ?? [];
        $facturantePredeterminado = $data['facturante_predeterminado'] ?? null;
        // 1. Buscar email existente
        $existingEmail = UserEmail::where('email', $email)->first();
        if ($existingEmail) {
            if ($existingEmail->verificado) {
                throw new \Exception('Usuario existente ', 409);
            } else {
                // Eliminar usuario completo (esto eliminará en cascada emails y teléfonos)
                $userToDelete = User::where('id_mail_principal', $existingEmail->id)
                    ->orWhereHas('emails', function ($query) use ($email) {
                        $query->where('email', $email);
                    })
                    ->first();

                if ($userToDelete) {
                    $userToDelete->delete(); // Esto eliminará todo en cascada
                } else {
                    $existingEmail->delete();
                }
            }
        }

        $password = $this->generarPasswordAvanzado();
        $usr = $this->emailService->enviarCorreoHijo($email, $password);

        // 3. Crear nuevo usuario
        $passwordHash = Hash::make($password);
        $user = User::create([
            'password'  => $passwordHash,
            'idRol'     =>  3,
            'usuario_padre' => $idPadre,
            'password_temporal' => true
        ]);

        // 4. Guardar correo y teléfono
        $correo = $user->emails()->create([
            'email' => $email,
        ]);



        if (!empty($facturantes)) {
            $this->asignarFacturantesEnTransaccion($user->id, $facturantes, $facturantePredeterminado, $idPadre);
        }


        $user->update([
            'id_mail_principal' => $correo->id,
            'datos_fiscales_principal' => $facturantePredeterminado
        ]);

        return $user;
    }

    protected function asignarFacturantesEnTransaccion($idHijo, $facturantes, $facturantePredeterminado, $idPadre)
    {
        // Verificar que los datos fiscales pertenecen al padre
        $facturantesValidos = DatosFiscal::where('id_usuario', $idPadre)
            ->whereIn('id', $facturantes)
            ->pluck('id')
            ->toArray();

        if (count($facturantes) !== count($facturantesValidos)) {
            throw new \Exception('Uno o más facturantes no pertenecen al usuario padre', 400);
        }

        // Validar facturante predeterminado
        if ($facturantePredeterminado && !in_array($facturantePredeterminado, $facturantesValidos)) {
            throw new \Exception('El facturante predeterminado no pertenece al usuario padre', 400);
        }

        // Asignar facturantes
        $facturantesConPivot = [];
        foreach ($facturantesValidos as $facturanteId) {
            $facturantesConPivot[$facturanteId] = [
                'predeterminado' => ($facturanteId == $facturantePredeterminado)
            ];
        }

        $userHijo = User::find($idHijo);
        $userHijo->facturantesPermitidos()->sync($facturantesConPivot);
    }


    public function completarHijo(array $data)
    {
        $email = $data['email'];
        $tel   = $data['tel'];
        $user = $this->findByEmailOrUser($email);
        $existingPhone = UserPhone::where('telefono', $tel)->first();
        if ($existingPhone) {
            if ($existingPhone->verificado) {
                throw new \Exception('Usuario existente ', 409);
            } else {
                // Eliminar usuario completo
                $userToDelete = User::where('id_telefono_principal', $existingPhone->id)
                    ->orWhereHas('phones', function ($query) use ($tel) {
                        $query->where('telefono', $tel);
                    })
                    ->first();

                if ($userToDelete) {
                    $userToDelete->delete(); // Esto eliminará todo en cascada
                } else {
                    $existingPhone->delete();
                }
            }
        }

        // 3. Crear nuevo usuario
        $data['password'] = Hash::make($data['password']);



        $telefono = $user->phones()->create([
            'telefono' => $tel,
        ]);

        $user->update([
            'password'  => $data['password'],
            'id_telefono_principal' => $telefono->id,
            'password_temporal' => false
        ]);
        return $user;
    }
    /**
     * Actualizar facturantes de un usuario hijo existente
     */
    public function actualizarFacturantesHijo($idHijo, array $data)
    {
        $facturantes = $data['facturantes'] ?? [];
        $facturantePredeterminado = $data['facturante_predeterminado'] ?? null;

        $userHijo = User::where('idRol', 3)->findOrFail($idHijo);
        $idPadre = $userHijo->usuario_padre;

        if (empty($facturantes)) {
            // Si no se envían facturantes, eliminar todas las asignaciones
            $userHijo->facturantesPermitidos()->detach();
            return;
        }

        //$this->asignarFacturantesAHijo($idHijo, $facturantes, $facturantePredeterminado, $idPadre);
    }

    function generarPasswordAvanzado($longitudMin = 8, $longitudMax = 10)
    {
        $longitud = random_int($longitudMin, $longitudMax);

        $caracteres = [
            'mayusculas' => 'ABCDEFGHJKLMNPQRSTUVWXYZ', // Eliminé I, O para evitar confusión
            'minusculas' => 'abcdefghjkmnpqrstuvwxyz',  // Eliminé i, l, o
            'numeros' => '23456789',                    // Eliminé 0, 1
            'especiales' => '!@#$%&*+-=?'
        ];

        $password = '';

        // Un carácter de cada tipo
        foreach ($caracteres as $tipo => $caracteresTipo) {
            $password .= $caracteresTipo[random_int(0, strlen($caracteresTipo) - 1)];
        }

        // Todos los caracteres combinados
        $todos = implode('', $caracteres);

        // Completar
        for ($i = strlen($password); $i < $longitud; $i++) {
            $password .= $todos[random_int(0, strlen($todos) - 1)];
        }

        // Convertir a array, mezclar y volver a string
        $passwordArray = str_split($password);
        shuffle($passwordArray);

        return implode('', $passwordArray);
    }


    public function update(array $data, $id)
    {
        return User::whereId($id)->update($data);
    }

    public function updatePassword(array $data, $id)
    {
        $data['password'] = Hash::make($data['password']);
        return User::whereId($id)->update($data);
    }


    public function aumentarIntento(int $intentos, $id)
    {
        User::where('id', $id)->update(array('intentos' => $intentos + 1));
    }

    public function generateToken(User $user): string
    {
        $token = $user->createToken('API Token');
        return $token->plainTextToken;
    }

    public function loginActive(int $id)
    {
        User::where('id', $id)->update(array('login_activo' => true));
    }

    public function loginInactive(int $id)
    {
        User::where('id', $id)->update(array('login_activo' => false));
    }

    public function deleteUser(array $data, $id)
    {
        return User::whereId($id)->update($data);
    }

    public function desencriptarAES($textoEncriptado)
    {
        $clave = "mi_clave_super_secreta_123";
        $cifra = "AES-256-CBC";
        $key = hash('sha256', $clave, true); // misma clave que en JS
        $iv = substr($key, 0, 16); // IV derivado de la clave (debe coincidir con JS si no usas uno dinámico)

        $textoEncriptado = base64_decode($textoEncriptado);
        return openssl_decrypt($textoEncriptado, $cifra, $key, OPENSSL_RAW_DATA, $iv);
    }
}
