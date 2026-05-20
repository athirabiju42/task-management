<?php

return [
    'provider' => env('AI_PROVIDER', 'mock'),
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
    ],
    'queue_ai' => env('AI_QUEUE_PROCESSING', false),
];
