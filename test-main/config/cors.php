
//<?php
//
//return [
//    'paths' => ['api/*', 'sanctum/csrf-cookie'],
//    'allowed_methods' => ['*'],
//    'allowed_origins' => ['http://localhost:3000', 'http://127.0.0.1:3000', 'http://localhost:53589', '*'],
//    'allowed_origins_patterns' => [],
//    'allowed_headers' => ['*'],
//    'exposed_headers' => [],
//    'max_age' => 0,
//    'supports_credentials' => true,
//];

return [
    'paths' => [
        'api/*', 
        'sanctum/csrf-cookie',
        'login',
        'logout',
        'register',
        'storage/*', // ← ADD THIS LINE - MOST IMPORTANT!
    ],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:3000', 
        'http://127.0.0.1:3000', 
        'http://localhost:53589',
        'http://localhost',  // ← Add this
        'http://localhost:8000', // ← Add this
        'http://192.168.*', // ← Allow local network IPs
        '*'
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
