<?php
/**
 * Standardized HTTP JSON Response Helper
 */

declare(strict_types=1);

namespace Atelier\Core;

class Response {
    /**
     * Send standard JSON output and terminate execution
     */
    public static function json(array $payload, int $status = 200, array $headers = []): void {
        if (!headers_sent()) {
            http_response_code($status);
            header("Content-Type: application/json; charset=UTF-8");
            foreach ($headers as $key => $value) {
                header("{$key}: {$value}");
            }
        }

        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * Return successful response
     */
    public static function success(mixed $data = null, string $message = '', int $status = 200, array $meta = []): void {
        $payload = [
            'success' => true
        ];

        if ($message !== '') {
            $payload['message'] = $message;
        }

        if ($data !== null) {
            $payload['data'] = $data;
        }

        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }

        self::json($payload, $status);
    }

    /**
     * Return error response
     */
    public static function error(string $message, int $status = 400, array $errors = []): void {
        $payload = [
            'success' => false,
            'error'   => $message
        ];

        if (!empty($errors)) {
            $payload['errors'] = $errors;
        }

        self::json($payload, $status);
    }

    /**
     * 400 Bad Request
     */
    public static function badRequest(string $message = 'Bad request.', array $errors = []): void {
        self::error($message, 400, $errors);
    }

    /**
     * 401 Unauthorized
     */
    public static function unauthorized(string $message = 'Authentication required.'): void {
        self::error($message, 401);
    }

    /**
     * 403 Forbidden
     */
    public static function forbidden(string $message = 'Access denied.'): void {
        self::error($message, 403);
    }

    /**
     * 404 Not Found
     */
    public static function notFound(string $message = 'Resource not found.'): void {
        self::error($message, 404);
    }

    /**
     * 405 Method Not Allowed
     */
    public static function methodNotAllowed(string $allowed = 'GET'): void {
        self::error("Method Not Allowed. Permitted: {$allowed}", 405);
    }

    /**
     * 422 Unprocessable Entity (Validation failed)
     */
    public static function validationFailed(array $errors, string $message = 'Validation failed.'): void {
        self::error($message, 422, $errors);
    }

    /**
     * 500 Internal Server Error
     */
    public static function serverError(string $message = 'Internal server error.'): void {
        self::error($message, 500);
    }
}
