<?php
/**
 * HTTP Request Abstraction & Input Sanitizer
 */

declare(strict_types=1);

namespace Atelier\Core;

class Request {
    private string $method;
    private array $query;
    private array $body;
    private array $headers;

    public function __construct() {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->query = $_GET ?? [];
        $this->headers = $this->extractHeaders();
        $this->body = $this->parseBody();
    }

    /**
     * Get HTTP Method
     */
    public function getMethod(): string {
        return $this->method;
    }

    /**
     * Check if current request matches method
     */
    public function isMethod(string $method): bool {
        return $this->method === strtoupper($method);
    }

    /**
     * Ensure request method matches expected, or trigger 405 error
     */
    public function requireMethod(string $method): void {
        if (!$this->isMethod($method)) {
            Response::methodNotAllowed($method);
        }
    }

    /**
     * Extract and parse body from JSON or Form POST
     */
    private function parseBody(): array {
        if ($this->isMethod('GET')) {
            return [];
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        // Standard multipart/form-data or application/x-www-form-urlencoded
        if (!empty($_POST)) {
            return $_POST;
        }

        // Fallback for raw JSON without explicit content-type header
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    /**
     * Retrieve parameter from Query (GET)
     */
    public function query(string $key, mixed $default = null): mixed {
        return $this->query[$key] ?? $default;
    }

    /**
     * Retrieve parameter from Body (POST / PUT / JSON)
     */
    public function input(string $key, mixed $default = null): mixed {
        return $this->body[$key] ?? $default;
    }

    /**
     * Retrieve parameter from either Body or Query
     */
    public function get(string $key, mixed $default = null): mixed {
        if (array_key_exists($key, $this->body)) {
            return $this->body[$key];
        }
        if (array_key_exists($key, $this->query)) {
            return $this->query[$key];
        }
        return $default;
    }

    /**
     * Get all body parameters
     */
    public function all(): array {
        return $this->body;
    }

    /**
     * Check if a key exists and is non-empty
     */
    public function has(string $key): bool {
        $val = $this->get($key);
        return $val !== null && $val !== '';
    }

    /**
     * Sanitize string input
     */
    public function getClean(string $key, string $default = ''): string {
        $val = $this->get($key, $default);
        if (!is_string($val)) {
            return $default;
        }
        return trim(strip_tags($val));
    }

    /**
     * Extract Bearer token from Authorization header if present
     */
    public function getBearerToken(): ?string {
        $header = $this->headers['authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s(\S+)/i', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Normalize headers to lowercase keys
     */
    private function extractHeaders(): array {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headerName = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$headerName] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'])) {
                $headerName = strtolower(str_replace('_', '-', $key));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }
}
