<?php
declare(strict_types=1);

// Contrôleur frontal : toutes les pages passent par index.php?page=...

define('ROOT', dirname(__DIR__));

require ROOT . '/controller/publicController.php';
require ROOT . '/controller/userController.php';

$page = $_GET['page'] ?? 'accueil';

switch ($page) {
    case 'accueil':
        homePage();
        break;
    case 'recettes':
        recipesPage();
        break;
    case 'recette':
        recipeDetailPage((string) ($_GET['slug'] ?? ''));
        break;
    case 'eshop':
        shopPage();
        break;
    case 'a-propos':
        aboutPage();
        break;
    case 'contact':
        contactPage();
        break;
    case 'connexion':
        loginPage();
        break;
    case 'inscription':
        registerPage();
        break;
    case 'favoris':
        favoritesPage();
        break;
    default:
        notFoundPage();
}
