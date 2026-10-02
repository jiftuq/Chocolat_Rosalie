<?php

namespace App\Manager;

class RatingManager extends AbstractManager
{
    /**
     * Enregistre ou remplace la note d'un utilisateur (BE-21).
     * La clé primaire (user_id, recipe_id) + ON DUPLICATE KEY UPDATE rendent
     * l'opération atomique : deux envois simultanés ne créent pas de doublon.
     */
    public function rate(int $userId, int $recipeId, int $rating): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO ratings (user_id, recipe_id, rating)
             VALUES (:user, :recipe, :rating)
             ON DUPLICATE KEY UPDATE rating = VALUES(rating), rated_at = CURRENT_TIMESTAMP'
        );
        $stmt->execute(['user' => $userId, 'recipe' => $recipeId, 'rating' => $rating]);
    }

    /** @return array{average: ?float, count: int} moyenne à une décimale (BE-22) */
    public function getStats(int $recipeId): array
    {
        $stmt = $this->pdo->prepare('SELECT AVG(rating) AS average, COUNT(*) AS total FROM ratings WHERE recipe_id = :id');
        $stmt->execute(['id' => $recipeId]);
        $row = $stmt->fetch();
        return [
            'average' => $row['average'] === null ? null : round((float) $row['average'], 1),
            'count' => (int) $row['total'],
        ];
    }

    public function getUserRating(int $userId, int $recipeId): ?int
    {
        $stmt = $this->pdo->prepare('SELECT rating FROM ratings WHERE user_id = :user AND recipe_id = :recipe');
        $stmt->execute(['user' => $userId, 'recipe' => $recipeId]);
        $rating = $stmt->fetchColumn();
        return $rating === false ? null : (int) $rating;
    }
}
