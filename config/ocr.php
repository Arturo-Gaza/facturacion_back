<?php

return [
    'providers' => [
        'google_vision' => [
            'api_key' => env('GOOGLE_VISION_API_KEY'),
            'api_url' => env('GOOGLE_VISION_API_URL'),
            'enabled' => true,
        ],
        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'api_url' => env('GEMINI_API_URL'),
            'enabled' => true,
        ],
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'api_url' => 'https://api.openai.com/v1/chat/completions',
            'enabled' => false,
        ],
    ],

    'default_provider' => 'google_vision',
    'default_ai_provider' => 'gemini',
    
    'timeout' => 30,
    'retry_attempts' => 3,
];