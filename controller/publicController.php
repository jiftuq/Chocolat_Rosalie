<?php

require_once ROOT . '/model/recipes.php';
require_once ROOT . '/model/products.php';

/** Échappe une valeur pour l'affichage HTML. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Chemin public d'une image de public/assets/img. */
function img(string $file): string
{
    return 'assets/img/' . $file;
}

/** URL d'une page du site. */
function url(string $page, array $params = []): string
{
    return 'index.php?' . http_build_query(['page' => $page] + $params);
}

/** Affiche une vue dans le gabarit commun (en-tête + pied de page). */
function render(string $view, array $data = []): void
{
    $data += [
        'title' => 'Maison Rosalie',
        'bodyClass' => '',
        'floatingFooter' => false,
        'footerRecipes' => getRecipes(),
    ];
    extract($data, EXTR_SKIP);

    ob_start();
    require ROOT . '/view/' . $view . '.php';
    $content = ob_get_clean();

    require ROOT . '/view/layout.php';
}

function homePage(): void
{
    render('accueil', [
        'title' => 'Maison Rosalie — Chocolatier belge depuis 1928',
        'floatingFooter' => true,
        'pralines' => [
            ['name' => 'La cerisette', 'image' => 'praline-cerise.png', 'slug' => 'cerisette', 'w' => 218, 'h' => 230],
            ['name' => 'La citronelle', 'image' => 'praline-citron.png', 'slug' => 'citronnelle', 'w' => 217, 'h' => 198],
            ['name' => 'L’orangette', 'image' => 'praline-orange.png', 'slug' => 'orangette', 'w' => 199, 'h' => 214],
            ['name' => 'La framboisette', 'image' => 'praline-framboise.png', 'slug' => null, 'w' => 207, 'h' => 218],
        ],
    ]);
}

function recipesPage(): void
{
    render('recipe', [
        'title' => 'Recettes — Maison Rosalie',
        'recipes' => getRecipes(),
    ]);
}

function recipeDetailPage(string $slug): void
{
    $recipe = getRecipe($slug);
    if ($recipe === null) {
        notFoundPage();
        return;
    }
    render('recipeDetail', [
        'title' => 'La Praline ' . $recipe['name'] . ' — Maison Rosalie',
        'recipe' => $recipe,
    ]);
}

function shopPage(): void
{
    render('eshop', [
        'title' => 'E-shop — Maison Rosalie',
        'categories' => getShopCategories(),
        'sections' => getShopSections(),
    ]);
}

function aboutPage(): void
{
    render('about', ['title' => 'À propos — Maison Rosalie']);
}

function contactPage(): void
{
    render('contact', ['title' => 'Contact — Maison Rosalie']);
}

function notFoundPage(): void
{
    http_response_code(404);
    render('404', ['title' => 'Page introuvable — Maison Rosalie']);
}
