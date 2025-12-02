<?php

namespace App\Services;

use App\DTOs\CheckIdResultDto;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class CheckIdService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = env('CHECK_ID_URL');
        $this->apiKey = env('CHECK_ID_KEY'); // lo leeremos del config
    }

   public function buscar($termino)
{
    $payload = [
        "ApiKey" => $this->apiKey,
        "TerminoBusqueda" => $termino,
        "ObtenerRFC" => true,
        "ObtenerCURP" => true,
        "Obtener69o69B" => false,
        "ObtenerNSS" => false,
        "ObtenerRegimenFiscal" => true,
        "ObtenerCP" => true,
    ];

    // Opciones de Guzzle: connect_timeout = tiempo para conectar/DNS,
    // timeout = tiempo total para la respuesta (segundos).
    $options = [
        'connect_timeout' => 10, // si falla resolver/connect en 10s -> fallo rápido
        'timeout' => 60,        // espera hasta 60s por la respuesta completa
        // Debug opcional para trazar en servidor (descomentar para usar)
        // 'debug' => fopen(storage_path('logs/checkid_curl_debug_' . time() . '.log'), 'w'),
        // 'on_stats' => function (\GuzzleHttp\TransferStats $stats) { /* puedes loggear $stats */ },
    ];

    $start = microtime(true);

    try {
        $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->withOptions($options)
            // reintenta 3 veces con 1s, 2s, 4s de espera (exponential backoff)
            ->retry(3, 1000, function ($exception, $request) {
                // reintentar sobre excepciones de red y 5xx del servidor
                return $exception instanceof ConnectionException
                    || (method_exists($exception, 'getCode') && intval($exception->getCode()) >= 500);
            })
            ->post($this->baseUrl, $payload);

        $duration = microtime(true) - $start;



        // Si no hay cuerpo o viene vacío
        $body = $response->body();
        if (empty($body)) {
            throw new \Exception("CheckID: respuesta vacía (0 bytes) — termino: {$termino}");
        }

        $respuesta = $response->json();

    } catch (\Throwable $e) {
        $duration = microtime(true) - $start;


        // Re-lanzar con info resumida (o retorna null / objeto con error si prefieres)
        throw new \Exception("Error en CheckID: {$e->getMessage()}");
    }

    // Valida formato esperado
    if (!isset($respuesta['exitoso'])) {

        throw new \Exception("CheckID: formato de respuesta inesperado");
    }

    if (!$respuesta['exitoso']) {
        // incluye error recibido si lo hay
        $err = $respuesta['error'] ?? json_encode($respuesta);
        throw new \Exception("CheckID (no exitoso): {$err}");
    }

    $resultado = $respuesta['resultado'] ?? $respuesta;

    return new CheckIdResultDto($resultado);
}

}
