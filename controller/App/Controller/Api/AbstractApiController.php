<?php

namespace App\Controller\Api;

use App\Core\Database;
use App\Core\Session;
use App\Exception\UnauthorizedException;
use PDO;

abstract class AbstractApiController
{
    protected function pdo(): PDO
    {
        return Database::getConnection();
    }

    /** Corps de la requête : JSON (fetch) ou formulaire classique. */
    protected function input(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $decoded = json_decode(file_get_contents('php://input') ?: '', true);
            return is_array($decoded) ? $decoded : [];
        }
        return $_POST;
    }

    /** Contrôle d'accès côté serveur sur chaque action (SEC-06). */
    protected function requireUser(): array
    {
        return Session::user() ?? throw new UnauthorizedException();
    }

    protected function queryString(string $key): string
    {
        $value = $_GET[$key] ?? '';
        return is_string($value) ? trim($value) : '';
    }

    protected function queryInt(string $key, int $default, int $min, int $max): int
    {
        $value = filter_var($_GET[$key] ?? $default, FILTER_VALIDATE_INT);
        return $value === false ? $default : max($min, min($max, $value));
    }

    protected function clientIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
