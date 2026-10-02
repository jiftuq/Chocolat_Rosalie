<?php use App\Core\View; ?>
<section class="hero" aria-labelledby="hero-title">
    <div class="hero__veil"></div>
    <div class="container hero__content text-center">
        <img class="hero__logo" src="<?= View::asset('img/logo-vertical.png') ?>" alt="" width="260" height="82">
        <h1 class="hero__title" id="hero-title">Maison Rosalie</h1>
        <p class="hero__slogan">Des créations d’exception pour des moments précieux</p>
        <a class="btn btn-caramel btn-lg" href="<?= View::url('recettes') ?>">Découvrir nos recettes</a>
    </div>
</section>

<section class="section" aria-labelledby="top-title">
    <div class="container">
        <p class="section__kicker">Les préférées des gourmands</p>
        <h2 class="section__title" id="top-title">Les 3 recettes les mieux notées</h2>
        <div class="row g-4 justify-content-center" data-top-recipes aria-live="polite">
            <div class="col-12 text-center py-5" data-state="loading">
                <div class="spinner-border text-caramel" role="status"><span class="visually-hidden">Chargement des recettes…</span></div>
            </div>
        </div>
    </div>
</section>

<section class="section section--rose" aria-labelledby="story-title">
    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-6 order-lg-2">
                <img class="story__img" src="<?= View::asset('img/atelier.webp') ?>" alt="Mains d’artisan versant du chocolat fondu dans l’atelier" width="736" height="736" loading="lazy">
            </div>
            <div class="col-lg-6">
                <p class="section__kicker">Notre maison</p>
                <h2 class="section__title text-start" id="story-title">Un savoir-faire de famille</h2>
                <p class="lead">Depuis 1987, la famille Moreau perpétue la tradition des chocolatiers artisanaux belges. Chaque recette est transmise de génération en génération avec amour et savoir-faire.</p>
                <a class="btn btn-brown" href="<?= View::url('a-propos') ?>">Notre histoire</a>
            </div>
        </div>
    </div>
</section>
