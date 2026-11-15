<?php
/**
 * Lightweight Native Environment (.env) Loader
 * Loads key-value configuration into getenv(), $_ENV, and $_SERVER without external dependencies.
 */

declare(strict_types=1);

namespace Atelier\Config;

class Env {
    private static bool $loaded = false;

    /**
     * Locate and parse .env file
     */
    public static function load(?string $filePath = null): void {
        if (self::$loaded && $filePath === null) {
            return;
        }

        $candidates = $filePath !== null ? [$filePath] : [
            realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . '.env',
            realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR . '.env',
            __DIR__ . DIRECTORY_SEPARATOR . '.env'
        ];

        $targetFile = null;
        foreach ($candidates as $cand) {
            if ($cand && file_exists($cand) && is_readable($cand)) {
                $targetFile = $cand;
                break;
            }
        }

        if (!$targetFile) {
            // No .env file found; system environment variables will be used
            return;
        }

        $lines = file($targetFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            // Ignore comments and malformed lines
            if ($line === '' || str_starts_with($line, '#') || str_starts_with($line, ';') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Strip surrounding quotes
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            } else {
                // Strip inline comments if not quoted
                $commentPos = strpos($value, ' #');
                if ($commentPos !== false) {
                    $value = trim(substr($value, 0, $commentPos));
                }
            }

            // Populate environments if key is non-empty
            if ($key !== '') {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    /**
     * Helper to read an environment variable with type-casting and default fallback
     */
    public static function get(string $key, mixed $default = null): mixed {
        if (!self::$loaded) {
            self::load();
        }

        $val = $_ENV[$key] ?? ($_SERVER[$key] ?? getenv($key));

        if ($val === false || $val === null || $val === '') {
            return $default;
        }

        $lower = strtolower((string)$val);
        if ($lower === 'true' || $lower === '(true)') return true;
        if ($lower === 'false' || $lower === '(false)') return false;
        if ($lower === 'null' || $lower === '(null)') return null;
        if ($lower === 'empty' || $lower === '(empty)') return '';

        return $val;
    }
}

// Register global env() function if not already present
if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed {
        return Env::get($key, $default);
    }
}
