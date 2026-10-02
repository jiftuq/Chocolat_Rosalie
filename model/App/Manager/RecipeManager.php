<?php

namespace App\Manager;

use App\Model\Recipe;
use App\Model\RecipeIngredient;
use App\Model\Step;

class RecipeManager extends AbstractManager
{
    /**
     * Base commune : recette + moyenne et nombre de votes calculés
     * à partir des notes enregistrées (BE-23) + catégories.
     */
    private const SELECT_WITH_STATS = <<<SQL
        SELECT r.id, r.title, r.slug, r.description, r.main_image,
               r.prep_time_minutes, r.cook_time_minutes, r.portions,
               r.difficulty, r.created_at,
               (SELECT AVG(ra.rating) FROM ratings ra WHERE ra.recipe_id = r.id) AS average_rating,
               (SELECT COUNT(*) FROM ratings ra WHERE ra.recipe_id = r.id) AS rating_count,
               (SELECT GROUP_CONCAT(c.title ORDER BY c.title SEPARATOR '|')
                  FROM recipe_categories rc
                  JOIN categories c ON c.id = rc.category_id
                 WHERE rc.recipe_id = r.id) AS category_names
          FROM recipes r
        SQL;

    /**
     * Liste légère pour le menu déroulant (BE-10).
     * @return Recipe[]
     */
    public function getMenu(): array
    {
        $stmt = $this->pdo->query('SELECT id, title, slug FROM recipes ORDER BY title');
        return array_map(fn (array $row) => new Recipe($row), $stmt->fetchAll());
    }

    /**
     * Toutes les recettes avec note moyenne et nombre de votes (BE-11).
     * @return Recipe[]
     */
    public function getAllWithStats(): array
    {
        $stmt = $this->pdo->query(self::SELECT_WITH_STATS . ' ORDER BY r.title');
        return array_map(fn (array $row) => new Recipe($row), $stmt->fetchAll());
    }

    /**
     * Top de l'accueil (BE-30) : recettes notées d'abord, par moyenne
     * décroissante, puis nombre de votes, puis la plus récente.
     * S'il y a moins de recettes notées que demandé, les plus récentes
     * non notées complètent la liste.
     * @return Recipe[]
     */
    public function getTop(int $limit = 3): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM (' . self::SELECT_WITH_STATS . ') AS stats
             ORDER BY (rating_count = 0), average_rating DESC, rating_count DESC, created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return array_map(fn (array $row) => new Recipe($row), $stmt->fetchAll());
    }

    /** Détail complet d'une recette à partir de son identifiant lisible (BE-12). */
    public function getBySlug(string $slug): ?Recipe
    {
        $stmt = $this->pdo->prepare(self::SELECT_WITH_STATS . ' WHERE r.slug = :slug');
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        if ($row === false) {
            return null;
        }

        $recipe = new Recipe($row);
        $recipe->setIngredients($this->getIngredients($recipe->getId()));
        $recipe->setSteps($this->getSteps($recipe->getId()));
        return $recipe;
    }

    public function getIdBySlug(string $slug): ?int
    {
        $stmt = $this->pdo->prepare('SELECT id FROM recipes WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        $id = $stmt->fetchColumn();
        return $id === false ? null : (int) $id;
    }

    /** @return RecipeIngredient[] */
    private function getIngredients(int $recipeId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT i.name, ri.quantity, ri.unit
               FROM recipe_ingredients ri
               JOIN ingredients i ON i.id = ri.ingredient_id
              WHERE ri.recipe_id = :id
              ORDER BY ri.quantity IS NULL, i.name'
        );
        $stmt->execute(['id' => $recipeId]);
        return array_map(fn (array $row) => new RecipeIngredient($row), $stmt->fetchAll());
    }

    /** @return Step[] */
    private function getSteps(int $recipeId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, step_number, title, description, image
               FROM steps WHERE recipe_id = :id ORDER BY step_number'
        );
        $stmt->execute(['id' => $recipeId]);
        return array_map(fn (array $row) => new Step($row), $stmt->fetchAll());
    }
}
