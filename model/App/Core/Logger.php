<?php

namespace App\Core;

use Throwable;

/**
 * Journal des erreurs techniques (SEC-10) : le visiteur ne voit jamais
 * ces détails, ils sont écrits dans logs/app.log, hors du dossier public.
 */
final class Logger
{
    public static function error(Throwable $e): void
    {
        $line = sprintf(
            "[%s] %s: %s in %s:%d\n",
            date('Y-m-d H:i:s'),
            $e::class,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );
        error_log($line, 3, RACINE_PATH . '/logs/app.log');
    }
}
