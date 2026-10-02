<section class="home-hero">
    <img class="home-hero__cacao" src="<?= img('cacao-botanique.png') ?>" alt="" width="424" height="424">
    <div class="home-hero__text">
        <h1 class="home-hero__title">Nos créations,<br>pour vos moments !</h1>
        <p class="home-hero__lead">Découvrez nos nouvelle recettes.<br>Une création printanière inspirée des fruits confits.<br>Des saveurs délicates, imaginées pour surprendre.</p>
        <a class="btn-pill btn-pill--lg" href="<?= url('recettes') ?>">Découvrez nos recettes</a>
    </div>
    <img class="home-hero__praline" src="<?= img('praline-orange.png') ?>" alt="Praline l’orangette en trompe-l’œil" width="442" height="476">
</section>

<section class="home-collection">
    <h2 class="home-collection__kicker">une nouvelle page se dévoile</h2>
    <p class="home-collection__sub">quatre créations, quatre éclats de gourmandise.</p>
    <ul class="praline-row">
        <?php foreach ($pralines as $praline): ?>
            <li>
                <a class="praline-tile" href="<?= $praline['slug'] ? url('recette', ['slug' => $praline['slug']]) : url('recettes') ?>">
                    <span class="praline-tile__img">
                        <img src="<?= img($praline['image']) ?>" alt="" width="<?= $praline['w'] ?>" height="<?= $praline['h'] ?>">
                    </span>
                    <span class="praline-tile__name"><?= e($praline['name']) ?></span>
                </a>
            </li>
        <?php endforeach; ?>
        <li class="praline-row__more">
            <a href="<?= url('recettes') ?>" aria-label="Voir toutes les recettes">
                <img src="<?= img('arrow-up.svg') ?>" alt="" width="23" height="26">
            </a>
        </li>
    </ul>
</section>

<section class="home-film">
    <img class="home-film__gif" src="<?= img('recette-maison-rosalie.gif') ?>" alt="Préparation des recettes Maison Rosalie" width="1440" height="810" loading="lazy">
    <?php require ROOT . '/view/partials/footer.php'; ?>
</section>
