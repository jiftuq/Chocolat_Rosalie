<?php
$half = (int) ceil(count($recipe['ingredients']) / 2);
$ingredientColumns = [
    array_slice($recipe['ingredients'], 0, $half),
    array_slice($recipe['ingredients'], $half),
];
?>
<section class="recipe-hero">
    <p class="page-title page-title--left">Recette de Maison Rosalie</p>
    <div class="recipe-hero__grid">
        <div class="recipe-hero__intro">
            <h1 class="praline-name">La Praline <span style="color: <?= e($recipe['color']) ?>"><?= e($recipe['name']) ?></span></h1>
            <img class="recipe-hero__rule" src="<?= img('line12.svg') ?>" alt="" width="169" height="1">
            <p class="recipe-hero__desc"><?= nl2br(e($recipe['description'])) ?></p>
        </div>
        <img class="recipe-hero__cut <?= e($recipe['cutClass']) ?>" src="<?= img($recipe['cut']) ?>" alt="La praline <?= e($recipe['name']) ?> coupée" width="311" height="311">
        <img class="recipe-hero__photo" src="<?= img('recettes/atelier.jpg') ?>" alt="Préparation de la ganache à l’atelier" width="349" height="349">
        <img class="recipe-hero__praline" src="<?= img($recipe['hero']) ?>" alt="La praline <?= e($recipe['name']) ?>" style="--tilt: <?= (int) $recipe['heroTilt'] ?>deg">
    </div>
</section>

<section class="reviews" aria-labelledby="reviews-title">
    <div class="reviews__summary">
        <h2 class="reviews__title" id="reviews-title">Avis de gourmands</h2>
        <p class="reviews__subtitle">Vos commentaires nous touchent<br>et nous inspirent</p>
        <div class="reviews__score">
            <span class="reviews__note">4.9</span>
            <span class="stars" aria-label="4,5 étoiles sur 5">
                <?php for ($i = 0; $i < 4; $i++): ?><img src="<?= img('star.svg') ?>" alt="" width="32" height="32"><?php endfor; ?>
                <img src="<?= img('star-half.svg') ?>" alt="" width="32" height="32">
            </span>
        </div>
        <p class="reviews__count">Basé sur 20 avis <img src="<?= img('icon-heart.svg') ?>" alt="" width="10" height="9"> <img class="reviews__count-line" src="<?= img('line27.svg') ?>" alt="" width="90" height="1"></p>
    </div>

    <form class="review-form" method="post" action="<?= url('recette', ['slug' => $recipe['slug']]) ?>">
        <div class="review-form__head">
            <h2 class="review-form__title"><img src="<?= img('icon-pen.svg') ?>" alt="" width="26" height="26"> Laisser un avis</h2>
            <fieldset class="review-form__stars">
                <legend class="sr-only">Votre note</legend>
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input class="sr-only" type="radio" name="rating" id="rating-<?= $i ?>" value="<?= $i ?>">
                    <label for="rating-<?= $i ?>" title="<?= $i ?> / 5"><img src="<?= img('star-outline.svg') ?>" alt="<?= $i ?> étoiles" width="32" height="32"></label>
                <?php endfor; ?>
            </fieldset>
        </div>
        <p class="review-form__hint">Partagez votre expérience et aidez d’autres gourmands à découvrir cette recette !</p>
        <label class="sr-only" for="review-message">Votre commentaire</label>
        <textarea class="review-form__input" id="review-message" name="message" placeholder="Votre commentaire..." maxlength="500" required></textarea>
        <button class="review-form__submit" type="submit">Publier</button>
    </form>

    <ul class="reviews__cards">
        <?php foreach (['review-1.png', 'review-2.png', 'review-3.png'] as $card): ?>
            <li><img src="<?= img($card) ?>" alt="Avis client" loading="lazy" width="337" height="168"></li>
        <?php endforeach; ?>
    </ul>
</section>

<section class="recipe-section">
    <h2 class="recipe-section__title">Les ingrédients <img src="<?= img('icon-mortar.svg') ?>" alt="" width="92" height="92"></h2>
    <img class="recipe-section__rule" src="<?= img('line-hr.svg') ?>" alt="" width="1441" height="1">
    <div class="ingredients">
        <?php foreach ($ingredientColumns as $column): ?>
            <ul class="ingredients__col">
                <?php foreach ($column as [$name, $image]): ?>
                    <li>
                        <img src="<?= img($image) ?>" alt="" width="135" height="140" loading="lazy">
                        <span><?= e($name) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    </div>
</section>

<section class="recipe-section">
    <h2 class="recipe-section__title">La préparation <img class="is-mirrored" src="<?= img('icon-whisk.svg') ?>" alt="" width="87" height="87"></h2>
    <img class="recipe-section__rule" src="<?= img('line-hr.svg') ?>" alt="" width="1441" height="1">
    <ol class="steps">
        <?php foreach ($recipe['steps'] as $step): ?>
            <li class="step">
                <h3 class="step__title"><?= e($step['title']) ?></h3>
                <p class="step__text"><?= nl2br(e($step['text'])) ?></p>
                <img class="step__img" src="<?= img($step['image']) ?>" alt="" width="196" height="196" loading="lazy">
            </li>
        <?php endforeach; ?>
    </ol>
</section>

<ul class="recipe-facts">
    <li><img src="<?= img('icon-time.svg') ?>" alt="" width="90" height="90"><span><span class="sr-only">Temps : </span><?= e($recipe['time']) ?></span></li>
    <li><img src="<?= img('icon-chef-hat.svg') ?>" alt="" width="90" height="90"><span><span class="sr-only">Difficulté : </span><?= e($recipe['difficulty']) ?></span></li>
    <li><img src="<?= img('icon-euro.svg') ?>" alt="" width="90" height="90"><span><span class="sr-only">Coût : </span><?= e($recipe['cost']) ?></span></li>
</ul>
