<?php
// Contrôleur frontal des pages HTML : index.php?page=...

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Controller\PageController;
use App\Core\Logger;
use App\Exception\ConfigurationException;

$controller = new PageController();
$page = is_string($_GET['page'] ?? null) ? $_GET['page'] : 'accueil';

try {
    match ($page) {
        'accueil' => $controller->home(),
        'recettes' => $controller->recipes(),
        'recette' => $controller->recipe(is_string($_GET['slug'] ?? null) ? $_GET['slug'] : ''),
        'a-propos' => $controller->about(),
        'contact' => $controller->contact(),
        default => $controller->notFound(),
    };
} catch (Throwable $e) {
    // base coupée ou toute autre panne : message propre, détails dans logs/ (SEC-10)
    Logger::error($e);
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    $showDetail = APP_DEBUG || $e instanceof ConfigurationException;
    $controller->unavailable($showDetail ? $e->getMessage() : null);
}
