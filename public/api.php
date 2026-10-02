<?php
// Point d'entrée de l'API JSON : api.php?route=... (documentation : docs/API.md)

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Controller\Api\AuthController;
use App\Controller\Api\CommentController;
use App\Controller\Api\ContactController;
use App\Controller\Api\RatingController;
use App\Controller\Api\RecipeController;
use App\Core\JsonResponse;
use App\Core\Logger;
use App\Core\Session;
use App\Exception\ForbiddenException;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Exception\ValidationException;

// route => [méthode HTTP => [contrôleur, action]]
$routes = [
    'auth/me' => ['GET' => [AuthController::class, 'me']],
    'auth/register' => ['POST' => [AuthController::class, 'register']],
    'auth/login' => ['POST' => [AuthController::class, 'login']],
    'auth/logout' => ['POST' => [AuthController::class, 'logout']],
    'recipes/menu' => ['GET' => [RecipeController::class, 'menu']],
    'recipes' => ['GET' => [RecipeController::class, 'index']],
    'recipes/top' => ['GET' => [RecipeController::class, 'top']],
    'recipe' => ['GET' => [RecipeController::class, 'show']],
    'ratings' => ['POST' => [RatingController::class, 'store']],
    'comments' => [
        'GET' => [CommentController::class, 'index'],
        'POST' => [CommentController::class, 'store'],
        'DELETE' => [CommentController::class, 'destroy'],
    ],
    'contact' => ['POST' => [ContactController::class, 'store']],
];

$route = is_string($_GET['route'] ?? null) ? $_GET['route'] : '';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    if (!isset($routes[$route])) {
        throw new NotFoundException('Point d’accès inconnu.');
    }
    if (!isset($routes[$route][$method])) {
        header('Allow: ' . implode(', ', array_keys($routes[$route])));
        throw new HttpException('Méthode non autorisée.', 405);
    }
    // toute action qui modifie des données exige le jeton CSRF (SEC-03)
    if ($method !== 'GET' && !Session::isValidCsrf($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
        throw new ForbiddenException('Votre session a expiré. Rechargez la page puis réessayez.');
    }

    [$class, $action] = $routes[$route][$method];
    (new $class())->$action();
} catch (ValidationException $e) {
    JsonResponse::error($e->getMessage(), $e->getStatus(), $e->getErrors());
} catch (HttpException $e) {
    JsonResponse::error($e->getMessage(), $e->getStatus());
} catch (Throwable $e) {
    // aucun détail technique pour le visiteur (BE-13, SEC-10)
    Logger::error($e);
    // en développement (APP_DEBUG), on montre la cause pour faciliter le diagnostic
    $detail = APP_DEBUG ? ' [' . $e::class . ' : ' . $e->getMessage() . ']' : '';
    JsonResponse::error('Le service est momentanément indisponible. Merci de réessayer dans quelques instants.' . $detail, 503);
}
