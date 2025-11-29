<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\DTOs\UserProfileDTO;
use App\Http\Requests\StoreUsuarioRequest;
use App\Interfaces\Usuario\UsuarioRepositoryInterface;
use App\Models\Catalogos\CatRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\UserEmail;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Exception;
use Google_Client;
use Illuminate\Support\Facades\Log;
use PragmaRX\Google2FA\Google2FA;

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
    public function redirectToGoogle(Request $request)
    {
        $transactionKey = request('transaction_key');

        // 2. Guardarla en la sesión de Laravel temporalmente
        if ($transactionKey) {
            session(['google_auth_key' => $transactionKey]);
        }
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $transactionKey = session('google_auth_key');

            // 2. Limpiar la sesión inmediatamente
            session()->forget('google_auth_key');
            session()->forget('google_token');
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Extraer nombre y apellidos del nombre completo
            $name = $googleUser->getName();
            $nameParts = explode(' ', $name);
            $user = $this->userRepo->findByEmailOrUser($googleUser->email);
            if ($user) {
                $user->update([
                    'intentos' => 0,
                    'login_activo' => true,
                ]);
            } else {
                // Crear el usuario primero
                $user = User::create([

                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => null,
                    'intentos' => 0,
                    'login_activo' => true,
                    'idRol' => 2,
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
            $userProfile = UserProfileDTO::fromUserModel($user);
            // Generar el contenido HTML del blade
            $viewContent = view('google-callback', [

                'token' => $token,
                'tokenGoogle' => $tokenGoogle,
                'name' => $nameParts[0] ?? '',
                'primer_apellido' => $nameParts[1] ?? null,
                'segundo_apellido' => $nameParts[2] ?? null,
                'user' => $userProfile,
                'transactionKey' => $transactionKey
            ])->render();

            // Retornar la respuesta con el encabezado COOP
            return response($viewContent)
                ->header('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');
        } catch (\Throwable $e) {
            dd('stateless failed', $e->getMessage(), $e->getTraceAsString());
        } catch (\Exception $e) {
            Log::error('Error Google Auth: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error en autenticación',
                'message' => $e->getMessage() // Solo en desarrollo
            ], 500);
        }
    }

    // En tu AuthController o nuevo controller
    public function mobileGoogleAuth(Request $request)
    {
        try {
            DB::beginTransaction();

            // 1. Validar que viene el token
            $request->validate([
                'idToken' => 'required|string'
            ]);

            $idToken = $request->idToken;

            // 2. Verificar el token con Google
            $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($idToken);

            if (!$payload) {
                return response()->json([
                    'error' => 'Token inválido'
                ], 401);
            }

            // 3. Extraer datos del usuario (VERIFICADOS por Google)
            $googleId = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'];
            $avatar = $payload['picture'] ?? null;
            $emailVerified = $payload['email_verified'] ?? false;

            // 4. Buscar o crear usuario (usa tu lógica existente)
            $user = $this->userRepo->findByEmailOrUser($email);

            if ($user) {
                $user->update([
                    'google_id' => $googleId,
                    'avatar' => $avatar,
                    'intentos' => 0,
                    'login_activo' => true,
                ]);
            } else {
                // Crear el usuario (similar a tu lógica actual)
                $user = User::create([
                    'google_id' => $googleId,
                    'avatar' => $avatar,
                    'password' => null,
                    'intentos' => 0,
                    'login_activo' => true,
                    'idRol' => 2,
                ]);

                // Crear el registro en user_emails
                $userEmail = UserEmail::create([
                    'user_id' => $user->id,
                    'email' => $email,
                    'verificado' => $emailVerified,
                ]);

                $user->update(['id_mail_principal' => $userEmail->id]);
            }

            // 5. Generar tokens para tu app
            $token = $user->createToken('mobile-app')->plainTextToken;
            $this->userRepo->loginActive($user->id);

            DB::commit();
            // 6. Devolver respuesta JSON para mobile
            return ApiResponseHelper::sendResponse($user, $user->wasRecentlyCreated ? 'Usuario registrado' : 'Login exitoso', 201, $token);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error Mobile Google Auth: ' . $e->getMessage());
            return ApiResponseHelper::throw(null, $e->getMessage(), 500);
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
    public function register(Request $request)
    {
        try {
            $user = $this->userRepo->store($request->all());
            return ApiResponseHelper::sendResponse($user, 'Registro insertado correctamente', 201);
        } catch (Exception $e) {
            return ApiResponseHelper::throw(null, $e->getMessage(), $e->getCode());
        }
    }
    public function registerCliente(Request $request)
    {
        try {
            $user = $this->userRepo->storeCliente($request->all());
            return ApiResponseHelper::sendResponse($user, 'Registro insertado correctamente', 201);
        } catch (Exception $e) {
            $status = (int) $e->getCode();
            if ($status < 100 || $status >= 600) {
                $status = 500;
            }

            return ApiResponseHelper::throw(null, $e->getMessage(), $status);
        }
    }
    public function registerHijo(Request $request)
    {
        try {

            $user = $this->userRepo->storeHijo($request->all());
            return ApiResponseHelper::sendResponse($user, 'Uasuario registrado correctamente', 201);
        } catch (Exception $e) {
            return ApiResponseHelper::throw(null, $e->getMessage(), 400);
        }
    }
    public function completarHijo(Request $request)
    {
        try {

            $user = $this->userRepo->completarHijo($request->all());
            return ApiResponseHelper::sendResponse($user, 'Registro insertado correctamente', 201);
        } catch (Exception $e) {
            return ApiResponseHelper::throw(null, $e->getMessage(), 400);
        }
    }
    public function updateHijo(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_hijo' => 'required|integer|exists:users,id',
                'id_usuario' => 'required|integer|exists:users,id', // padre
                'email' => 'email',
                'facturantes' => 'required|array',
                'facturantes.*' => 'integer|exists:datos_fiscales,id',
                'facturante_predeterminado' => 'required|integer|exists:datos_fiscales,id'
            ]);
            $user = $this->userRepo->updateHijo($validated);
            return ApiResponseHelper::sendResponse($user, 'Registro insertado correctamente', 201);
        } catch (Exception $e) {
            return ApiResponseHelper::throw(null, $e->getMessage(), 400);
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
        $rolesUsrsFinales = CatRoles::where('recupera_gastos', true)
            ->pluck('id')
            ->toArray();
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = $this->userRepo->findByEmailOrUser($request->email);
        if ($user == null || !in_array($user->idRol, $rolesUsrsFinales)) {
            return ApiResponseHelper::rollback(null, 'El usuario no esta registrado', 401);
        }


        // Validar si el correo está verificado
        if ($user->mailPrincipal && !$user->mailPrincipal->verificado && !$user->password_temporal) {
            return ApiResponseHelper::rollback(null, 'El correo electrónico no ha sido verificado', 401);
        }

        // Validar si el teléfono está verificado
        if ($user->password == null) {
            return ApiResponseHelper::rollback(null, 'La contraseña temporal es de un solo uso y ya fue utilizada, favor de ingresar a cambiar contraseña para crear una nueva', 401);
        }
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
        if ($user->password_temporal) {
            if ($user->mailPrincipal) {
                $user->mailPrincipal->verificado = true;
                $user->mailPrincipal->save();
            }

            $user->password = null;
            $user->save();
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
    }

    public function loginEmpleados(Request $request)
    {
        $rolesUsrsFinales = CatRoles::where('consola', true)
            ->pluck('id')
            ->toArray();
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = $this->userRepo->findByEmailOrUser($request->email);
        if ($user == null || !in_array($user->idRol, $rolesUsrsFinales)) {
            return ApiResponseHelper::rollback(null, 'Credenciales no válidas ', 401);
        }


        // Validar si el correo está verificado
        if ($user->mailPrincipal && !$user->mailPrincipal->verificado && !$user->password_temporal) {
            return ApiResponseHelper::rollback(null, 'El correo electrónico no ha sido verificado', 401);
        }

        // Validar si el teléfono está verificado
        if ($user->password == null) {
            return ApiResponseHelper::rollback(null, 'La contraseña temporal es de un solo uso y ya fue utilizada, favor de ingresar a cambiar contraseña para crear una nueva', 401);
        }
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
        if ($user->password_temporal) {
            if ($user->mailPrincipal) {
                $user->mailPrincipal->verificado = true;
                $user->mailPrincipal->save();
            }

            $user->password = null;
            $user->password_temporal = false;
            $user->save();
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
            'two_factor_required' => false,

        ], 200);
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


    //Controller para la doble Autenticacion
    public function enable2FA(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Si el usuario ya tiene activado el 2FA, solo devuelve un mensaje
        if (!is_null($user->two_factor_secret)) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario ya tiene habilitada la autenticación de dos factores.'
            ], 200);
        }

        // Crear nueva clave secreta
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $secret = $google2fa->generateSecretKey();

        $user->two_factor_secret = $secret;
        $user->save();

        // Generar URL y QR
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            'Recupera Gastos',
            $user->email,
            $secret
        );

        // Usamos QuickChart para generar la imagen QR
        $qrImage = 'https://quickchart.io/qr?text=' . urlencode($qrCodeUrl) . '&size=200&margin=1';

        return response()->json([
            'success' => true,
            'message' => 'Se ha generado el código QR para la autenticación de dos factores.',
            'secret' => $secret,
            'qr_image' => $qrImage,
            'qr_url' => $qrCodeUrl
        ]);
    }

    public function verify2FA(Request $request)
    {
        $user = User::find($request->id);
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if ($valid) {

            return response()->json([
                'success' => true,
                'message' => 'Código válido'
            ]);
        } else {
            return ApiResponseHelper::rollback(null, 'Código incorrecto ', 401);
        }
    }

    // Genera QR de Google Authenticator

}
