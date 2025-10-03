<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\DTOs\UserProfileDTO;
use App\Http\Requests\StoreUsuarioRequest;
use App\Interfaces\Usuario\UsuarioRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\UserEmail;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Info(
 *      title="Api SIAC",
 *      version="1.0",
 *      description="SIAC"
 * )
 * @OA\Server(url="http://localhost:8000")
 */
class AuthController extends Controller
{
    protected $userRepo;

    public function __construct(UsuarioRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }


    public function handleGoogleCallback()
    {
        try {
            session()->forget('google_token');
            $googleUser = Socialite::driver('google')->user();

            // Extraer nombre y apellidos del nombre completo
            $name = $googleUser->getName();
            $nameParts = explode(' ', $name);
            $user = $this->userRepo->findByEmailOrUser($googleUser->email);
            if ($user) {
                $user->update([
                    'name' => $nameParts[0] ?? '',
                    'apellidoP' => $nameParts[1] ?? null,
                    'apellidoM' => $nameParts[2] ?? null,
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),

                    'intentos' => 0,
                    'login_activo' => true,
                    'idRol' => 2,
                    'email_verified_at' => now(),
                ]);
            } else {
                // Crear el usuario primero
                $user = User::create([
                    'name' => $nameParts[0] ?? '',
                    'apellidoP' => $nameParts[1] ?? null,
                    'apellidoM' => $nameParts[2] ?? null,
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => null,
                    'user' => strtolower(str_replace(' ', '', $googleUser->getName())),
                    'intentos' => 0,
                    'login_activo' => true,
                    'idRol' => 2,
                    'email_verified_at' => now(),
                    // id_mail_principal se establecerá después de crear el email
                ]);

                // Crear el registro en user_emails
                $userEmail = UserEmail::create([
                    'user_id' => $user->id,
                    'email' => $googleUser->email,
                    'verificado' => true,
                ]);

                // Actualizar el usuario con el id_mail_principal
                $user->update(['id_mail_principal' => $userEmail->id]);
            }


            $userresponse = $this->userRepo->responseUser($user->email);
            // Generar token de acceso (si es una API)
            $tokenGoogle = $user->createToken('google-token')->plainTextToken;
            $token = $this->userRepo->generateToken($user);
            try {
                $this->userRepo->loginActive($user->id);

                DB::commit();
            } catch (Exception $ex) {
                DB::rollBack();
                return ApiResponseHelper::rollback($ex);
            }
            return view('google-callback', [
                'user' => $userresponse,
                'token' => $token,
                'tokenGoogle' => $tokenGoogle,
            ]);
        } catch (\Exception $e) {
            Log::error('Error Google Auth: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error en autenticación',
                'message' => $e->getMessage() // Solo en desarrollo
            ], 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"Users"},
     *     summary="Crear nuevo usuario",
     *     description="Crear nuevo usuario",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="ejemplo@email.com"),
     *             @OA\Property(property="password", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Record created successfully"
     *     )
     * )
     */
    public function register(StoreUsuarioRequest $request)
    {
        $user = $this->userRepo->store($request->all());
        return ApiResponseHelper::sendResponse($user, 'Registro insertado correctamente', 201);
    }
    public function registerCliente(Request $request)
    {
        try {

            $user = $this->userRepo->storeCliente($request->all());
            return ApiResponseHelper::sendResponse($user, 'Registro insertado correctamente', 201);
        } catch (Exception $e) {
             return ApiResponseHelper::throw(null, $e->getMessage(), $e->getCode());
        }
    }
        public function registerHijo(Request $request)
    {
        try {

            $user = $this->userRepo->storeHijo($request->all());
            return ApiResponseHelper::sendResponse($user, 'Registro insertado correctamente', 201);
        } catch (Exception $e) {
             return ApiResponseHelper::throw(null, $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Users"},
     *     summary="Iniciar Sesión",
     *     description="Iniciar Sesión",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example=""),
     *             @OA\Property(property="password", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sesión iniciada correctamente"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = $this->userRepo->findByEmailOrUser($request->email);
        $userresponse = $this->userRepo->responseUser($request->email);
        if ($user == null) {
            return ApiResponseHelper::rollback(null, 'Credenciales no válidas ', 401);
        }


        // Validar si el correo está verificado
        if ($user->mailPrincipal && !$user->mailPrincipal->verificado && !$user->password_temporal) {
            return ApiResponseHelper::rollback(null, 'El correo electrónico no ha sido verificado', 401);
        }

        // Validar si el teléfono está verificado
        if ($user->telefonoPrincipal && !$user->telefonoPrincipal->verificado && !$user->password_temporal) {
            return ApiResponseHelper::rollback(null, 'El número de teléfono no ha sido verificado', 401);
        }

        if ($user->intentos >= 3) {
            return ApiResponseHelper::rollback(null, 'Ha excedido el número de intentos de inicio de sesión, favor de contactar con el administrador ', 401);
        } else {

            if ($user->id_estatus_usuario == 2) {

                return ApiResponseHelper::rollback(null, 'Usuario bloqueado', 401);
            } 
            if ($user->id_estatus_usuario == 3) {

                return ApiResponseHelper::rollback(null, 'Usuario dado de baja', 401);
            } else {
                if (!$user || !Hash::check($request->password, $user->password)) {
                    // $user->intentos=$user->intentos+1;
                    // $user->update($user->toArray(),$user->id);

                    //ESTE ME PERMITE EN AUMENTA EL NUMERO DE INTENTOS INTENTOS**
                    DB::beginTransaction();
                    try {
                        $this->userRepo->aumentarIntento($user->intentos, $user->id);

                        DB::commit();
                    } catch (Exception $ex) {
                        DB::rollBack();
                        return ApiResponseHelper::rollback($ex);
                    }

                    return ApiResponseHelper::rollback(null, 'Credenciales no válidas ', 401);
                }
            }
        }



        if ($user->two_factor_enabled) {
            // Generar código de verificación
            $user->generateTwoFactorCode();

            // Enviar código por email (puedes adaptar para SMS u otros métodos)
            // $user->notify(new TwoFactorCodeNotification());

            return response()->json([
                'success' => true,
                'message' => 'Se requiere verificación de dos factores',
                'two_factor_required' => true,
                'user_id' => $user->id,
            ], 200);
        }

        
        $token = $this->userRepo->generateToken($user);

        DB::beginTransaction();
        try {
            $this->userRepo->loginActive($user->id);

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }

        $userProfile = UserProfileDTO::fromUserModel($user);

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión correcto',
            'data' =>  $userProfile->toArray(),
            'token' => $token,

        ], 200);
        return ApiResponseHelper::sendResponse($userresponse, 'Usuario logueado correctamente', 201, $token);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/logout",
     *     tags={"Users"},
     *     summary="Cerrar Sesión",
     *     description="Cerrar Sesión",
     *     @OA\RequestBody(
     *         required=false,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sesión iniciada correctamente"
     *     )
     * )
     */
    public function logout(Request $request, $id)
    {
        $request->user()->tokens()->delete();

        DB::beginTransaction();
        try {
            $this->userRepo->loginInactive($id);

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::rollback($ex);
        }
        return response()->json(['message' => 'Logged out'], 200);
    }
}
