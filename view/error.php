<?php use App\Core\View; ?>
<section class="section error-page">
    <div class="container text-center">
        <img src="<?= View::asset('img/cacao.webp') ?>" alt="" width="200" height="200">
        <h1 class="page-header__title"><?= View::e($errorTitle) ?></h1>
        <p class="lead"><?= View::e($errorMessage) ?></p>
        <a class="btn btn-brown" href="<?= View::url('accueil') ?>">Retour à l’accueil</a>
        <a class="btn btn-outline-brown" href="<?= View::url('recettes') ?>">Voir les recettes</a>
    </div>
</section>
