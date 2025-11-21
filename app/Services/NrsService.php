<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use App\Models\PasswordConfPhone;
use App\Models\PasswordRecPhone;
use Carbon\Carbon;

class NrsService
{
    protected $url;
    protected $password;
    protected $from;
    protected $username;
    protected $appDebug;

    public function __construct()
    {
        $this->url = env('NRS_API_URL');
        $this->password = env('NRS_API_PASSWORD');
        $this->from = env('NRS_SENDER_NAME');
        $this->username = env('NRS_USERNAME');
        $this->appDebug = env('APP_DEBUG');
    }

    public function enviarSMSConf($to)
    {
        try {
            $basicToken = base64_encode("$this->username:$this->password");
            // Generamos el código
            $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $message = "El código de verificación es " . $codigo;

            // Guardamos en BD
            PasswordConfPhone::create([
                'phone' => $to,
                'codigo' => Hash::make($codigo),
                'created_at' => Carbon::now(),
            ]);
            if ($this->appDebug) {
                return "El codigo de validación es: . $codigo";
            }
            // Llamada a 360nrs
            $response = Http::withHeaders([
                'Authorization' => "Basic {$basicToken}",
                'Content-Type' => 'application/json',
            ])->post($this->url, [
                "to" => [$to],
                "from" => $this->from,
                "message" => $message
            ]);

            if (!$response->successful()) {
                throw new \Exception("Error enviando SMS: " . json_encode($response->json()));
            }
            return  $response->json();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function enviarSMSRec($to)
    {
        try {
            $basicToken = base64_encode("$this->username:$this->password");
            // Generamos el código
            $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $message = "El código para cambiar tu contraseña es " . $codigo;

            // Guardamos en BD
            PasswordRecPhone::create([
                'phone' => $to,
                'codigo' => Hash::make($codigo),
                'created_at' => Carbon::now(),
            ]);
            if ($this->appDebug) {
                return "El codigo de validación es: . $codigo";
            }
            // Llamada a 360nrs
            $response = Http::withHeaders([
                'Authorization' => "Basic {$basicToken}",
                'Content-Type' => 'application/json',
            ])->post($this->url, [
                "to" => [$to],
                "from" => $this->from,
                "message" => $message
            ]);

            if (!$response->successful()) {
                throw new \Exception("Error enviando SMS: " . json_encode($response->json()));
            }
            return  $response->json();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
