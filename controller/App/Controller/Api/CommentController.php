<?php

namespace App\Controller\Api;

use App\Core\JsonResponse;
use App\Core\Session;
use App\Core\Validator;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Exception\TooManyRequestsException;
use App\Manager\CommentManager;
use App\Manager\RecipeManager;
use App\Model\Comment;

class CommentController extends AbstractApiController
{
    public const PAGE_SIZE = 10;
    // Limitation (BE-44) : 5 commentaires maximum par tranche de 10 minutes
    private const MAX_PER_PERIOD = 5;
    private const PERIOD_MINUTES = 10;

    /** Liste paginée, du plus récent au plus ancien (BE-40). */
    public function index(): void
    {
        $recipeId = $this->recipeIdFromSlug($this->queryString('recipe'));
        $offset = $this->queryInt('offset', 0, 0, PHP_INT_MAX);

        $comments = new CommentManager($this->pdo());
        $items = $comments->getByRecipe($recipeId, self::PAGE_SIZE, $offset);
        $total = $comments->countByRecipe($recipeId);

        JsonResponse::success([
            'items' => array_map(fn (Comment $c) => $c->toArray($this->canDelete($c)), $items),
            'total' => $total,
            'nextOffset' => $offset + count($items),
            'hasMore' => $offset + count($items) < $total,
        ]);
    }

    /** Ajout réservé aux utilisateurs connectés (BE-41, BE-42). */
    public function store(): void
    {
        $user = $this->requireUser();

        $v = new Validator($this->input());
        $slug = $v->text('recipe');
        $subject = $v->text('subject');
        $message = $v->multiline('message');
        $v->maxLength('subject', $subject, 120, 'Le sujet')
            ->required('message', $message, 'Le message')
            ->length('message', $message, 3, 500, 'Le message')
            ->validate();

        $recipeId = $this->recipeIdFromSlug($slug);
        $comments = new CommentManager($this->pdo());
        if ($comments->countRecentByAuthor($user['id'], self::PERIOD_MINUTES) >= self::MAX_PER_PERIOD) {
            throw new TooManyRequestsException('Vous avez publié beaucoup de commentaires en peu de temps. Réessayez dans quelques minutes.');
        }

        $comment = $comments->add(new Comment([
            'author_id' => $user['id'],
            'recipe_id' => $recipeId,
            'subject' => $subject,
            'message' => $message,
        ]));

        JsonResponse::success([
            'comment' => $comment->toArray(true),
            'total' => $comments->countByRecipe($recipeId),
        ], 'Merci, votre commentaire est publié !', 201);
    }

    /** Suppression par l'auteur ou un administrateur uniquement (BE-43). */
    public function destroy(): void
    {
        $this->requireUser();
        $id = $this->queryInt('id', 0, 0, PHP_INT_MAX);

        $comments = new CommentManager($this->pdo());
        $comment = $comments->getById($id) ?? throw new NotFoundException('Ce commentaire n’existe pas.');
        if (!$this->canDelete($comment)) {
            throw new ForbiddenException('Vous ne pouvez supprimer que vos propres commentaires.');
        }

        $comments->delete($id);
        JsonResponse::success([
            'id' => $id,
            'total' => $comments->countByRecipe($comment->getRecipeId()),
        ], 'Commentaire supprimé.');
    }

    private function canDelete(Comment $comment): bool
    {
        return Session::isAdmin() || Session::userId() === $comment->getAuthorId();
    }

    private function recipeIdFromSlug(string $slug): int
    {
        return (new RecipeManager($this->pdo()))->getIdBySlug($slug)
            ?? throw new NotFoundException('Cette recette n’existe pas.');
    }
}
