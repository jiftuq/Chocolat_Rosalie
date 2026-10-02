<?php

namespace App\Manager;

use App\Model\Comment;
use PDO;

class CommentManager extends AbstractManager
{
    private const SELECT = <<<SQL
        SELECT c.id, c.author_id, c.recipe_id, c.subject, c.message, c.created_at,
               u.username AS author_name
          FROM comments c
          JOIN users u ON u.id = c.author_id
        SQL;

    /**
     * Commentaires publiés d'une recette, du plus récent au plus ancien,
     * par tranches (BE-40).
     * @return Comment[]
     */
    public function getByRecipe(int $recipeId, int $limit, int $offset): array
    {
        $stmt = $this->pdo->prepare(
            self::SELECT . " WHERE c.recipe_id = :recipe AND c.status = 'published'
             ORDER BY c.created_at DESC, c.id DESC
             LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue('recipe', $recipeId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return array_map(fn (array $row) => new Comment($row), $stmt->fetchAll());
    }

    public function countByRecipe(int $recipeId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM comments WHERE recipe_id = :recipe AND status = 'published'");
        $stmt->execute(['recipe' => $recipeId]);
        return (int) $stmt->fetchColumn();
    }

    public function getById(int $id): ?Comment
    {
        $stmt = $this->pdo->prepare(self::SELECT . ' WHERE c.id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : new Comment($row);
    }

    /** Ajoute le commentaire puis le relit pour renvoyer sa version complète (BE-42). */
    public function add(Comment $comment): Comment
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO comments (author_id, recipe_id, subject, message)
             VALUES (:author, :recipe, :subject, :message)'
        );
        $stmt->execute([
            'author' => $comment->getAuthorId(),
            'recipe' => $comment->getRecipeId(),
            'subject' => $comment->getSubject(),
            'message' => $comment->getMessage(),
        ]);
        return $this->getById((int) $this->pdo->lastInsertId());
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM comments WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /** Nombre de commentaires postés récemment par un utilisateur (BE-44). */
    public function countRecentByAuthor(int $authorId, int $minutes): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM comments
              WHERE author_id = :author AND created_at > (NOW() - INTERVAL :minutes MINUTE)'
        );
        $stmt->bindValue('author', $authorId, PDO::PARAM_INT);
        $stmt->bindValue('minutes', $minutes, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
