<?php
use App\Core\View;
use App\Model\Recipe;

/** @var Recipe $recipe @var ?int $userRating @var Recipe[] $otherRecipes */
$average = $recipe->getAverageRating();
$difficulty = $recipe->getDifficulty();
?>
<article class="recipe" data-recipe="<?= View::e($recipe->getSlug()) ?>">
    <header class="recipe-header">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <nav aria-label="Fil d’Ariane">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= View::url('recettes') ?>">Recettes</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= View::e($recipe->getTitle()) ?></li>
                        </ol>
                    </nav>
                    <?php foreach ($recipe->getCategoryNames() as $category): ?>
                        <span class="badge badge-category"><?= View::e($category) ?></span>
                    <?php endforeach; ?>
                    <h1 class="recipe-header__title"><?= View::e($recipe->getTitle()) ?></h1>
                    <p class="recipe-header__desc"><?= View::e($recipe->getDescription()) ?></p>
                    <p class="rating-summary" data-rating-summary>
                        <span data-stars="<?= View::e($average ?? 0) ?>"></span>
                        <span data-rating-text>
                            <?php if ($average === null): ?>
                                Pas encore noté
                            <?php else: ?>
                                <?= View::e(number_format($average, 1, ',', '')) ?>/5
                                (<?= $recipe->getRatingCount() ?> avis)
                            <?php endif; ?>
                        </span>
                    </p>
                </div>
                <div class="col-lg-6">
                    <img class="recipe-header__img" src="<?= View::asset('img/' . $recipe->getMainImage()) ?>"
                         alt="<?= View::e($recipe->getTitle()) ?>" width="742" height="800">
                </div>
            </div>
        </div>
    </header>

    <section class="facts" aria-label="Fiche rapide">
        <div class="container">
            <dl class="facts__list">
                <div class="facts__item">
                    <dt>Préparation</dt>
                    <dd><?= View::e(Recipe::formatDuration($recipe->getPrepTimeMinutes())) ?></dd>
                </div>
                <div class="facts__item">
                    <dt>Cuisson</dt>
                    <dd><?= View::e(Recipe::formatDuration($recipe->getCookTimeMinutes())) ?></dd>
                </div>
                <div class="facts__item">
                    <dt><img src="<?= View::asset('img/icon-time.svg') ?>" alt="" width="28" height="28"> Temps total</dt>
                    <dd><?= View::e(Recipe::formatDuration($recipe->getTotalTimeMinutes())) ?></dd>
                </div>
                <div class="facts__item">
                    <dt>Difficulté</dt>
                    <dd>
                        <span class="difficulty" aria-hidden="true">
                            <?php for ($i = 1; $i <= 3; $i++): ?>
                                <img class="<?= $i > $difficulty->level() ? 'is-off' : '' ?>" src="<?= View::asset('img/icon-chef-hat.svg') ?>" alt="" width="24" height="24">
                            <?php endfor; ?>
                        </span>
                        <?= View::e($difficulty->label()) ?>
                    </dd>
                </div>
                <div class="facts__item">
                    <dt>Portions</dt>
                    <dd><?= $recipe->getPortions() ?> pièces</dd>
                </div>
            </dl>
        </div>
    </section>

    <div class="container section">
        <div class="row g-4 g-lg-5">
            <section class="col-lg-4" aria-labelledby="ingredients-title">
                <div class="ingredients">
                    <h2 class="section__title text-start" id="ingredients-title">
                        <img src="<?= View::asset('img/icon-mortar.svg') ?>" alt="" width="40" height="40"> Ingrédients
                    </h2>
                    <ul class="ingredients__list">
                        <?php foreach ($recipe->getIngredients() as $ingredient): ?>
                            <li>
                                <span class="ingredients__qty"><?= View::e($ingredient->getDisplayQuantity()) ?></span>
                                <span><?= View::e($ingredient->getName()) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>

            <section class="col-lg-8" aria-labelledby="steps-title">
                <h2 class="section__title text-start" id="steps-title">
                    <img src="<?= View::asset('img/icon-whisk.svg') ?>" alt="" width="40" height="40"> Préparation
                </h2>
                <ol class="steps">
                    <?php foreach ($recipe->getSteps() as $step): ?>
                        <li class="step">
                            <img class="step__img" src="<?= View::asset('img/' . $step->getImage()) ?>"
                                 alt="Étape <?= $step->getStepNumber() ?> : <?= View::e($step->getTitle()) ?>" loading="lazy">
                            <div>
                                <h3 class="step__title"><span class="step__number"><?= $step->getStepNumber() ?></span> <?= View::e($step->getTitle()) ?></h3>
                                <p class="mb-0"><?= View::e($step->getDescription()) ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </section>
        </div>
    </div>

    <section class="section section--rose" aria-labelledby="opinion-title">
        <div class="container">
            <h2 class="section__title" id="opinion-title">Votre avis</h2>
            <div class="login-invite text-center" data-auth="guest"<?= $currentUser === null ? '' : ' hidden' ?>>
                <p>Connectez-vous pour noter cette recette et laisser un commentaire.</p>
                <button class="btn btn-brown" type="button" data-bs-toggle="modal" data-bs-target="#loginModal">Se connecter</button>
                <button class="btn btn-outline-brown" type="button" data-bs-toggle="modal" data-bs-target="#registerModal">Créer un compte</button>
            </div>
            <div class="row g-4" data-auth="user"<?= $currentUser === null ? ' hidden' : '' ?>>
                <div class="col-lg-4">
                    <form class="rating-form" data-rating-form>
                        <fieldset>
                            <legend class="h5">Votre note</legend>
                            <div class="star-input">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" class="visually-hidden" name="rating" id="rating-<?= $i ?>" value="<?= $i ?>"<?= $userRating === $i ? ' checked' : '' ?>>
                                    <label for="rating-<?= $i ?>" title="<?= $i ?> sur 5"><span class="visually-hidden"><?= $i ?> étoile<?= $i > 1 ? 's' : '' ?></span></label>
                                <?php endfor; ?>
                            </div>
                        </fieldset>
                        <p class="small mt-2 mb-0" data-rating-feedback>
                            <?= $userRating === null ? 'Cliquez sur une étoile pour noter.' : 'Votre note : ' . $userRating . '/5. Vous pouvez la modifier.' ?>
                        </p>
                    </form>
                </div>
                <div class="col-lg-8">
                    <form class="comment-form" data-comment-form novalidate>
                        <h3 class="h5">Laisser un commentaire</h3>
                        <div class="alert alert-danger d-none" role="alert" data-form-alert></div>
                        <div class="mb-3">
                            <label class="form-label" for="comment-subject">Sujet <span class="text-muted">(facultatif)</span></label>
                            <input class="form-control" type="text" id="comment-subject" name="subject" maxlength="120">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="comment-message">Message</label>
                            <textarea class="form-control" id="comment-message" name="message" rows="4" minlength="3" maxlength="500" aria-describedby="comment-counter" required></textarea>
                            <div class="form-text" id="comment-counter" data-counter>0 / 500 caractères</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <button class="btn btn-brown" type="submit">Publier</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="comments-title">
        <div class="container comments">
            <h2 class="section__title text-start" id="comments-title">Commentaires <span class="comments__count" data-comments-count></span></h2>
            <ul class="comments__list" data-comments-list aria-live="polite"></ul>
            <p class="text-muted" data-comments-state>Chargement des commentaires…</p>
            <button class="btn btn-outline-brown d-none" type="button" data-comments-more>Afficher plus de commentaires</button>
        </div>
    </section>

    <?php if ($otherRecipes !== []): ?>
        <nav class="section section--cream other-recipes" aria-labelledby="others-title">
            <div class="container">
                <h2 class="section__title" id="others-title">D’autres recettes à découvrir</h2>
                <ul class="other-recipes__list">
                    <?php foreach ($otherRecipes as $other): ?>
                        <li><a class="btn btn-outline-brown" href="<?= View::url('recette', ['slug' => $other->getSlug()]) ?>"><?= View::e($other->getTitle()) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </nav>
    <?php endif; ?>
</article>
