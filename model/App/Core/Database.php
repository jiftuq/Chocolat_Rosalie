<?php

namespace App\Core;

use PDO;
use App\Exception\ConfigurationException;

/**
 * Point unique de connexion à la base (QC-03).
 * La connexion est créée au premier appel puis réutilisée.
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            if (!defined('DB_HOST')) {
                throw new ConfigurationException('Installation incomplète : le fichier config.php est introuvable à la racine du projet (à côté de config.example.php).');
            }
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
            self::$pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$pdo;
    }
}
