<?php

namespace App\Controller\Api;

use App\Core\JsonResponse;
use App\Core\Session;
use App\Exception\NotFoundException;
use App\Manager\RatingManager;
use App\Manager\RecipeManager;
use App\Model\Recipe;

class RecipeController extends AbstractApiController
{
    /** Liste légère pour le menu (BE-10). */
    public function menu(): void
    {
        $recipes = (new RecipeManager($this->pdo()))->getMenu();
        JsonResponse::success(array_map(
            fn (Recipe $r) => ['title' => $r->getTitle(), 'slug' => $r->getSlug()],
            $recipes
        ));
    }

    /** Liste avec moyenne et nombre de votes (BE-11). */
    public function index(): void
    {
        $recipes = (new RecipeManager($this->pdo()))->getAllWithStats();
        JsonResponse::success(array_map(fn (Recipe $r) => $r->toSummaryArray(), $recipes));
    }

    /** Top 3 de l'accueil (BE-30). */
    public function top(): void
    {
        $recipes = (new RecipeManager($this->pdo()))->getTop(3);
        $data = [];
        foreach ($recipes as $index => $recipe) {
            $data[] = ['rank' => $index + 1] + $recipe->toSummaryArray();
        }
        JsonResponse::success($data);
    }

    /** Détail complet (BE-12) ; recette inconnue : 404 sans détail technique (BE-13). */
    public function show(): void
    {
        $recipe = (new RecipeManager($this->pdo()))->getBySlug($this->queryString('slug'))
            ?? throw new NotFoundException('Cette recette n’existe pas.');

        $userId = Session::userId();
        $userRating = $userId === null ? null : (new RatingManager($this->pdo()))->getUserRating($userId, $recipe->getId());
        JsonResponse::success($recipe->toDetailArray($userRating));
    }
}
