<?php
use App\Core\View;

// lien actif de la navigation (FE-06)
$current = fn (string $key) => $page === $key ? ' active" aria-current="page' : '';
?>
<header class="site-header">
    <nav class="navbar navbar-expand-lg" aria-label="Navigation principale">
        <div class="container">
            <a class="navbar-brand" href="<?= View::url('accueil') ?>">
                <img src="<?= View::asset('img/logo-mr.png') ?>" alt="Maison Rosalie — accueil" width="208" height="64">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Ouvrir le menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-lg-auto mb-3 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link<?= $current('accueil') ?>" href="<?= View::url('accueil') ?>">Accueil</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle<?= $page === 'recettes' ? ' active' : '' ?>" href="<?= View::url('recettes') ?>"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">Recettes</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item fw-semibold" href="<?= View::url('recettes') ?>">Toutes les recettes</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php if ($menuRecipes === null): ?>
                                <li><span class="dropdown-item-text text-muted small">Recettes momentanément indisponibles</span></li>
                            <?php else: ?>
                                <?php foreach ($menuRecipes as $menuRecipe): ?>
                                    <li><a class="dropdown-item" href="<?= View::url('recette', ['slug' => $menuRecipe->getSlug()]) ?>"><?= View::e($menuRecipe->getTitle()) ?></a></li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $current('a-propos') ?>" href="<?= View::url('a-propos') ?>">À propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $current('contact') ?>" href="<?= View::url('contact') ?>">Contact</a>
                    </li>
                </ul>
                <div class="account-area d-flex flex-wrap align-items-center gap-2">
                    <div class="d-flex gap-2" data-auth="guest"<?= $currentUser === null ? '' : ' hidden' ?>>
                        <button class="btn btn-outline-brown btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#loginModal">Connexion</button>
                        <button class="btn btn-brown btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#registerModal">Inscription</button>
                    </div>
                    <div class="d-flex align-items-center gap-2" data-auth="user"<?= $currentUser === null ? ' hidden' : '' ?>>
                        <span class="account-name">
                            <img src="<?= View::asset('img/icon-contact.svg') ?>" alt="" width="22" height="22">
                            <span data-username><?= View::e($currentUser['username'] ?? '') ?></span>
                        </span>
                        <button class="btn btn-outline-brown btn-sm" type="button" data-logout>Déconnexion</button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
