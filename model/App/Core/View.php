<?php

namespace App\Core;

/**
 * Rendu des vues : les fichiers de view/ ne font que de l'affichage (QC-02).
 */
final class View
{
    public static function render(string $view, array $data = [], int $status = 200): void
    {
        http_response_code($status);
        extract($data, EXTR_SKIP);

        ob_start();
        require RACINE_PATH . '/view/' . $view . '.php';
        $content = ob_get_clean();

        require RACINE_PATH . '/view/layout.php';
    }

    /** Échappement HTML de toute donnée affichée (SEC-02). */
    public static function e(string|int|float|null $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public static function url(string $page = 'accueil', array $params = []): string
    {
        $query = $page === 'accueil' && $params === [] ? '' : '?' . http_build_query(['page' => $page] + $params);
        return 'index.php' . $query;
    }

    public static function asset(string $path): string
    {
        return 'assets/' . $path;
    }
}
