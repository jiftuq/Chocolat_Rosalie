<?php

namespace App\Core;

/**
 * Structure de réponse homogène de l'API (BE-70) :
 * { "success": bool, "message": string, "data": mixed, "errors": object }
 */
final class JsonResponse
{
    public static function send(bool $success, string $message, mixed $data = null, int $status = 200, array $errors = []): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: no-store');

        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'errors' => (object) $errors,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function success(mixed $data = null, string $message = '', int $status = 200): never
    {
        self::send(true, $message, $data, $status);
    }

    public static function error(string $message, int $status, array $errors = []): never
    {
        self::send(false, $message, null, $status, $errors);
    }
}
