<?php

namespace App\Services;

use App\DTOs\CheckIdResultDto;
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
            "Obtener69o69B" => true,
            "ObtenerNSS" => true,
            "ObtenerRegimenFiscal" => true,
            "ObtenerCP" => true,
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])
        ->timeout(60)
        ->post($this->baseUrl, $payload);

        // Opcional: lanzar excepción si falla
        if ($response->failed()) {
            throw new \Exception("Error en CheckID: " . $response->body());
        }
        $respuesta = $response->json();
        $success = $respuesta["exitoso"];
        if (!$success) {
             throw new \Exception($respuesta["error"]);
        }
                $resultado = $respuesta['resultado'] ?? $respuesta;

        // crear DTO y devolver
        $dto = new CheckIdResultDto($resultado);

        return $dto;
    }
}
