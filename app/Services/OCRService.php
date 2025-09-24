<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OCRService
{
    private string $apiKey;
    private string $apiUrl;
    private string $provider;

    public function __construct(string $provider = null)
    {
        $this->provider = $provider ?: config('ocr.default_provider');
        $config = config("ocr.providers.{$this->provider}");
        
        $this->apiKey = $config['api_key'];
        $this->apiUrl = $config['api_url'];
    }

    public function extractTextFromImage(string $imageContent): ?string
    {
        if ($this->provider === 'google_vision') {
            return $this->extractWithGoogleVision($imageContent);
        }
        
        // Agregar más proveedores aquí
        return null;
    }

    private function extractWithGoogleVision(string $imageContent): ?string
    {
        try {
            $requestBody = [
                'requests' => [
                    [
                        'image' => ['content' => $imageContent],
                        'features' => [
                            ['type' => 'TEXT_DETECTION', 'maxResults' => 1]
                        ]
                    ]
                ]
            ];

            $response = Http::timeout(config('ocr.timeout'))
                ->retry(config('ocr.retry_attempts'), 1000)
                ->post("{$this->apiUrl}?key={$this->apiKey}", $requestBody);

            if ($response->successful()) {
                $data = $response->json();
                return $this->extractTextFromGoogleResponse($data);
            }

            Log::error("Error en OCR API: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("Error en OCR Service: " . $e->getMessage());
            return null;
        }
    }

    private function extractTextFromGoogleResponse(array $apiResponse): ?string
    {
        if (!isset($apiResponse['responses'][0]['textAnnotations'][0]['description'])) {
            return null;
        }

        $texto = $apiResponse['responses'][0]['textAnnotations'][0]['description'];
        return $this->cleanText($texto);
    }

    private function cleanText(string $texto): string
    {
        $texto = preg_replace('/\s+/', ' ', $texto);
        return trim($texto);
    }

    public function verifyConnection(): bool
    {
        try {
            $response = Http::timeout(10)
                ->get("{$this->apiUrl}?key={$this->apiKey}");
            return $response->status() !== 401;
        } catch (\Exception $e) {
            return false;
        }
    }
}