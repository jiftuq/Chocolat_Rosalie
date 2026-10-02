<section class="recipes">
    <h1 class="page-title page-title--left">Recette de Maison Rosalie</h1>
    <ul class="recipes__list">
        <?php foreach ($recipes as $recipe): ?>
            <li>
                <a class="recipe-tile" href="<?= url('recette', ['slug' => $recipe['slug']]) ?>">
                    <span class="praline-name">
                        La Praline<br><span style="color: <?= e($recipe['color']) ?>"><?= e($recipe['name']) ?></span>
                    </span>
                    <img class="recipe-tile__img" src="<?= img($recipe['cut']) ?>" alt="Praline <?= e($recipe['name']) ?> coupée en deux" loading="lazy">
                    <img class="recipe-tile__arrow" src="<?= img('arrow-up-outline.svg') ?>" alt="" width="32" height="32">
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
