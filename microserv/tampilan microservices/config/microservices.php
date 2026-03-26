<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Microservices API Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the microservices API endpoints and settings
    |
    */

    // Base URL untuk semua API calls
    'base_url' => env('MICROSERVICES_API_URL', 'http://localhost:3000'),

    // Service URLs (jika berbeda dari base gateway)
    'services' => [
        'gateway' => env('GATEWAY_URL', 'http://localhost:3000'),
        'user' => env('USER_SERVICE_URL', 'http://localhost:3001'),
        'appointment' => env('APPOINTMENT_SERVICE_URL', 'http://localhost:3002'),
        'medical' => env('MEDICAL_SERVICE_URL', 'http://localhost:3003'),
        'pharmacy' => env('PHARMACY_SERVICE_URL', 'http://localhost:3004'),
        'payment' => env('PAYMENT_SERVICE_URL', 'http://localhost:3005'),
        'analytics' => env('ANALYTICS_SERVICE_URL', 'http://localhost:3006'),
    ],

    // API request timeout (seconds)
    'timeout' => env('API_TIMEOUT', 30),

    // Retry configuration
    'retry' => [
        'enabled' => true,
        'times' => 3,
        'delay' => 100, // milliseconds
    ],

    // Cache configuration
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1 hour
    ],

    // Debug mode
    'debug' => env('API_DEBUG', false),
];
