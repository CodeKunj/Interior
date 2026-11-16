<?php
/**
 * Application Configuration & Environment Settings
 * Optimized for standard cPanel Shared/Cloud Hosting & Local Development.
 * Reads environment settings dynamically via Native .env Loader.
 */

declare(strict_types=1);

// Ensure Native .env Loader is always loaded and initialized
require_once __DIR__ . '/Env.php';
\Atelier\Config\Env::load();

$rawCors = env('CORS_ALLOWED_ORIGINS', 'http://localhost:5173,http://localhost:3000,http://127.0.0.1:5173,http://127.0.0.1:3000');
$allowedOrigins = is_array($rawCors) ? $rawCors : array_map('trim', explode(',', (string)$rawCors));

return [
    'app_name'    => env('APP_NAME', 'RiTiVerse • Atelier Vérité Interiors'),
    'app_env'     => env('APP_ENV', 'production'),
    'debug'       => env('APP_DEBUG', false),
    'app_url'     => env('APP_URL', 'https://interior.ritiverse.in'),

    // Database credentials (Reads from .env, or can be set directly for cPanel)
    'db' => [
        'host'    => env('DB_HOST', 'localhost'),
        'database'=> env('DB_NAME', 'ritivers_interior'),
        'username'=> env('DB_USER', 'root'),
        'password'=> env('DB_PASS', ''),
        'port'    => (int)env('DB_PORT', 3306),
        'charset' => 'utf8mb4'
    ],

    // Admin seed credentials
    'admin' => [
        'default_username' => env('ADMIN_USER', 'admin'),
        'default_password' => env('ADMIN_PASS', 'atelier2026Password!')
    ],

    // Permitted Cross-Origin Resource Sharing (CORS) origins
    'cors' => [
        'allowed_origins' => $allowedOrigins,
        'allowed_methods' => 'GET, POST, PUT, DELETE, OPTIONS',
        'allowed_headers' => 'Content-Type, Authorization, X-Requested-With, Accept',
        'max_age'         => 86400
    ],

    // File Upload Settings
    'uploads' => [
        'dir'               => __DIR__ . '/../../uploads',
        'url_prefix'        => '/uploads/',
        'max_file_size'     => 12 * 1024 * 1024, // 12 MB
        'allowed_extensions'=> ['jpg', 'jpeg', 'png', 'webp', 'avif'],
        'allowed_mimes'     => [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/avif'
        ]
    ],

    // Notification Mail Settings
    'mail' => [
        'admin_recipient' => env('MAIL_TO', 'concierge@ateliervérité.com'),
        'from_address'    => env('MAIL_FROM', 'no-reply@ateliervérité.com'),
        'from_name'       => env('MAIL_FROM_NAME', 'Atelier Vérité Studio Concierge'),
        'smtp_host'       => env('MAIL_SMTP_HOST', 'localhost'),
        'smtp_port'       => (int)env('MAIL_SMTP_PORT', 587)
    ]
];
