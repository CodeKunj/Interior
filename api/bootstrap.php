<?php
/**
 * Application Bootstrapper & Autoloader
 * Lightweight PSR-4 autoloader, unified error handling, and CORS lifecycle.
 * Runs on standard cPanel Shared Hosting without requiring Composer.
 */

declare(strict_types=1);

// -----------------------------------------------------------------------------
// 0. Native .env Environment Loader
// -----------------------------------------------------------------------------
require_once __DIR__ . '/config/Env.php';
\Atelier\Config\Env::load();

// Report all errors internally, but control display based on environment
error_reporting(E_ALL);

// Load App Configuration
$config = require __DIR__ . '/config/app.php';

// Configure Error Display based on debug setting
if (!empty($config['debug'])) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}

// -----------------------------------------------------------------------------
// 1. PSR-4 Native Autoloader for Atelier Namespace
// -----------------------------------------------------------------------------
spl_autoload_register(function (string $class) {
    $prefix = 'Atelier\\';
    $baseDir = __DIR__ . '/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Get the relative class name
    $relativeClass = substr($class, $len);

    // Map sub-namespaces to lower-case folder names
    // Example: Atelier\Core\Response -> api/core/Response.php
    $parts = explode('\\', $relativeClass);
    if (count($parts) > 1) {
        $folder = strtolower($parts[0]);
        $className = implode('/', array_slice($parts, 1));
        $file = $baseDir . $folder . '/' . $className . '.php';
    } else {
        $file = $baseDir . $relativeClass . '.php';
    }

    if (file_exists($file)) {
        require_once $file;
    }
});

// -----------------------------------------------------------------------------
// 2. Global CORS & Security Headers
// -----------------------------------------------------------------------------
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigins = $config['cors']['allowed_origins'] ?? [];

if (!empty($origin) && in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: {$origin}");
    header("Access-Control-Allow-Credentials: true");
} else {
    // In decoupled or local environments, permit all
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Methods: " . ($config['cors']['allowed_methods'] ?? 'GET, POST, PUT, DELETE, OPTIONS'));
header("Access-Control-Allow-Headers: " . ($config['cors']['allowed_headers'] ?? 'Content-Type, Authorization, X-Requested-With, Accept'));
header("Access-Control-Max-Age: " . ($config['cors']['max_age'] ?? 86400));
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");

// Fast exit for preflight OPTIONS queries
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// -----------------------------------------------------------------------------
// 3. Global Exception Handler (Always returns JSON for API endpoints)
// -----------------------------------------------------------------------------
set_exception_handler(function (\Throwable $exception) use ($config) {
    if (!headers_sent()) {
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code(500);
    }

    $response = [
        'success' => false,
        'error'   => 'An unexpected server error occurred.',
        'message' => $exception->getMessage()
    ];

    if (!empty($config['debug'])) {
        $response['debug'] = [
            'type'  => get_class($exception),
            'file'  => $exception->getFile(),
            'line'  => $exception->getLine(),
            'trace' => explode("\n", $exception->getTraceAsString())
        ];
    }

    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit();
});

return $config;
