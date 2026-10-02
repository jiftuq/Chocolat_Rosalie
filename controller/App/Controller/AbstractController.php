<?php

namespace App\Controller;

use App\Core\Database;
use App\Core\Logger;
use App\Core\Session;
use App\Core\View;
use App\Manager\RecipeManager;
use PDO;
use PDOException;
use RuntimeException;

abstract class AbstractController
{
    protected function pdo(): PDO
    {
        return Database::getConnection();
    }

    /**
     * Affiche une page avec les données communes au gabarit :
     * menu des recettes (depuis la base, FE-05), utilisateur connecté (FE-53), jeton CSRF.
     */
    protected function render(string $view, array $data, int $status = 200): void
    {
        View::render($view, $data + [
            'menuRecipes' => $this->loadSafely(fn () => (new RecipeManager($this->pdo()))->getMenu()),
            'currentUser' => Session::user(),
            'csrfToken' => Session::csrfToken(),
            'scripts' => [],
        ], $status);
    }

    /**
     * Exécute un chargement de données ; si la base est indisponible,
     * l'erreur est journalisée et la vue reçoit null pour afficher
     * un message propre au lieu d'un bloc cassé (QC-05).
     */
    protected function loadSafely(callable $loader): mixed
    {
        try {
            return $loader();
        } catch (PDOException | RuntimeException $e) {
            Logger::error($e);
            return null;
        }
    }
}
