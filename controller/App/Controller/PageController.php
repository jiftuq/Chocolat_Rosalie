<?php

namespace App\Controller;

use App\Core\Session;
use App\Manager\RatingManager;
use App\Manager\RecipeManager;
use App\Model\Recipe;

class PageController extends AbstractController
{
    public function home(): void
    {
        $this->render('home', [
            'page' => 'accueil',
            'title' => 'Maison Rosalie — Le livre de recettes au chocolat',
            'description' => 'Chocolaterie artisanale belge depuis 1987 : découvrez les recettes au chocolat de la famille Moreau, pas à pas.',
            'scripts' => ['home.js'],
        ]);
    }

    public function recipes(): void
    {
        $this->render('recipes', [
            'page' => 'recettes',
            'title' => 'Nos recettes — Maison Rosalie',
            'description' => 'Toutes les recettes au chocolat de la Maison Rosalie : pralines trompe-l’œil, gâteaux, desserts glacés.',
            'scripts' => ['recipes.js'],
        ]);
    }

    public function recipe(string $slug): void
    {
        $manager = new RecipeManager($this->pdo());
        $recipe = $manager->getBySlug($slug);
        if ($recipe === null) {
            $this->notFound('Cette recette n’existe pas (ou plus) dans notre livre.');
            return;
        }

        $userId = Session::userId();
        $userRating = $userId === null ? null : (new RatingManager($this->pdo()))->getUserRating($userId, $recipe->getId());
        $otherRecipes = array_filter(
            $manager->getMenu(),
            fn (Recipe $other) => $other->getSlug() !== $recipe->getSlug()
        );

        $this->render('recipe', [
            'page' => 'recettes',
            'title' => $recipe->getTitle() . ' — Maison Rosalie',
            'description' => mb_strimwidth($recipe->getDescription(), 0, 155, '…'),
            'recipe' => $recipe,
            'userRating' => $userRating,
            'otherRecipes' => $otherRecipes,
            'scripts' => ['recipe.js'],
        ]);
    }

    public function about(): void
    {
        $this->render('about', [
            'page' => 'a-propos',
            'title' => 'À propos — Maison Rosalie',
            'description' => 'Depuis 1987, la famille Moreau perpétue la tradition des chocolatiers artisanaux belges.',
        ]);
    }

    public function contact(): void
    {
        // horodatage du formulaire : un robot qui envoie en moins de 3 s est refusé (BE-50)
        Session::set('contact_form_started', time());
        $this->render('contact', [
            'page' => 'contact',
            'title' => 'Contact — Maison Rosalie',
            'description' => 'Une question, un atelier, une commande ? Écrivez à la Maison Rosalie, chocolaterie artisanale à Liège.',
            'scripts' => ['contact.js'],
        ]);
    }

    public function notFound(string $message = 'La page que vous cherchez n’existe pas ou a été déplacée.'): void
    {
        $this->render('error', [
            'page' => '',
            'title' => 'Page introuvable — Maison Rosalie',
            'description' => 'Cette page est introuvable.',
            'errorTitle' => 'Page introuvable',
            'errorMessage' => $message,
        ], 404);
    }

    public function unavailable(): void
    {
        $this->render('error', [
            'page' => '',
            'title' => 'Service indisponible — Maison Rosalie',
            'description' => 'Le site est momentanément indisponible.',
            'errorTitle' => 'Petit contretemps en cuisine',
            'errorMessage' => 'Nos recettes sont momentanément indisponibles. Merci de réessayer dans quelques instants.',
        ], 503);
    }
}
