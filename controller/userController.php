<?php

require_once ROOT . '/controller/publicController.php';

function loginPage(): void
{
    render('login', ['title' => 'Connexion — Maison Rosalie']);
}

function registerPage(): void
{
    render('register', ['title' => 'Créer un compte — Maison Rosalie']);
}

function favoritesPage(): void
{
    $favorites = array_filter([
        findProduct('tablette-citronelle'),
        findProduct('coffret-cadeau-rosalie'),
    ]);
    render('favorites', [
        'title' => 'Mes favoris — Maison Rosalie',
        'favorites' => $favorites,
    ]);
}
