<?php

namespace App\Repositories\Usuario;

use App\Interfaces\Usuario\UsuarioRepositoryInterface;
use App\Models\AsignacionCarga\tab_asignacion;
use App\Models\PasswordReset;
use App\Models\User;
use App\Models\UserSistema;
use App\Models\UsuarioRol;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioRepository implements UsuarioRepositoryInterface
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }


    public function getAll()
    {
        $usuario = User::all();

        return $usuario;
    }

    public function getCompras()
    {
        return User::where('idRol', 3)->get();
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

                $usr = User::where('email', $email)->first();
                $usr->password = $nuevaPass;
                $usr->save();
                return "Contraseña cambiada con exito";
            }
        }


        return "Error inesperado";
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

    public function findByEmailOrUser(string $email): ?User
    {
        return User::where('email', $email)->orWhere('user', $email)->first();
    }

    public function responseUser(string $email)
    {
        $usuario = User::where('users.email', $email)->orWhere('users.user', $email)->first();
        return $usuario;
    }

    public function store(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
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
