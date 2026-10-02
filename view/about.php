<?php use App\Core\View; ?>
<section class="page-header">
    <div class="container">
        <h1 class="page-header__title">À propos de Maison Rosalie</h1>
        <p class="page-header__lead">L’élégance du chocolat belge, de mère en fille, depuis 1987.</p>
    </div>
</section>

<section class="section pt-4" aria-labelledby="history-title">
    <div class="container">
        <div class="row g-4 g-lg-5 align-items-center">
            <div class="col-lg-6">
                <img class="about__img" src="<?= View::asset('img/about.webp') ?>" alt="L’atelier de la Maison Rosalie : chocolats, fèves de cacao et carnet de recettes" width="1400" height="933" loading="lazy">
            </div>
            <div class="col-lg-6">
                <h2 class="section__title text-start" id="history-title">Notre histoire</h2>
                <p>Tout commence en 1987 dans une petite boutique du quartier Saint-Gilles, à Liège. Rosalie Moreau, pâtissière de formation, y ouvre son premier atelier avec une conviction : un bon chocolat se travaille à la main, sans se presser.</p>
                <p>Ses recettes, notées à la plume dans des cahiers devenus précieux, sont transmises à ses enfants puis à ses petits-enfants. Aujourd’hui, la troisième génération de la famille Moreau partage ce livre de recettes avec vous, pour que chacun puisse reproduire chez soi les créations de la maison.</p>
            </div>
        </div>
    </div>
</section>

<section class="section section--rose" aria-labelledby="figures-title">
    <div class="container">
        <h2 class="section__title" id="figures-title">La maison en quelques chiffres</h2>
        <ul class="figures">
            <li><strong>1987</strong><span>année de création</span></li>
            <li><strong>3</strong><span>générations de chocolatiers</span></li>
            <li><strong>12</strong><span>artisans à l’atelier</span></li>
            <li><strong>80</strong><span>recettes dans le livre familial</span></li>
        </ul>
    </div>
</section>

<section class="section" aria-labelledby="values-title">
    <div class="container">
        <h2 class="section__title" id="values-title">Nos valeurs</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="value-card">
                    <h3>L’artisanat</h3>
                    <p class="mb-0">Chaque praline est moulée, garnie et fermée à la main, en petites séries.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card">
                    <h3>La qualité</h3>
                    <p class="mb-0">Des cacaos sélectionnés auprès de coopératives partenaires et des fruits de saison.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card">
                    <h3>La transmission</h3>
                    <p class="mb-0">Partager nos recettes, c’est faire vivre le savoir-faire des chocolatiers belges.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section--cream" aria-labelledby="timeline-title">
    <div class="container">
        <h2 class="section__title" id="timeline-title">Notre frise chronologique</h2>
        <ol class="timeline">
            <li><span class="timeline__year">1987</span> Rosalie Moreau ouvre sa boutique-atelier rue Saint-Gilles, à Liège.</li>
            <li><span class="timeline__year">1995</span> Création de la praline Orangette, devenue la signature de la maison.</li>
            <li><span class="timeline__year">2008</span> Sa fille Claire reprend l’atelier et lance les pralines trompe-l’œil.</li>
            <li><span class="timeline__year">2019</span> Ouverture d’ateliers de dégustation pour petits et grands.</li>
            <li><span class="timeline__year">2026</span> Le livre de recettes familial devient un site web ouvert à tous.</li>
        </ol>
    </div>
</section>
