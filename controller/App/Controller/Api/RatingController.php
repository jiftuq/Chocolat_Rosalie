<?php

namespace App\Controller\Api;

use App\Core\JsonResponse;
use App\Core\Validator;
use App\Exception\NotFoundException;
use App\Manager\RatingManager;
use App\Manager\RecipeManager;

class RatingController extends AbstractApiController
{
    /** Noter une recette (BE-20 à BE-22). */
    public function store(): void
    {
        $user = $this->requireUser();

        $v = new Validator($this->input());
        $slug = $v->text('recipe');
        // entier strict de 1 à 5 : 0, 6, 4.5 ou « abc » sont refusés (BE-20)
        $rating = $v->intInRange('rating', 1, 5, 'La note doit être un nombre entier de 1 à 5.');
        $v->validate();

        $recipeId = (new RecipeManager($this->pdo()))->getIdBySlug($slug)
            ?? throw new NotFoundException('Cette recette n’existe pas.');

        $ratings = new RatingManager($this->pdo());
        $ratings->rate($user['id'], $recipeId, $rating);
        $stats = $ratings->getStats($recipeId);

        JsonResponse::success([
            'average' => $stats['average'],
            'count' => $stats['count'],
            'userRating' => $rating,
        ], 'Merci pour votre note !');
    }
}
