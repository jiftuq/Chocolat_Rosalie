<?php /** @var array $product */ ?>
<article class="product-card" id="<?= e($product['id']) ?>">
    <div class="product-card__media">
        <img src="<?= img($product['image']) ?>" alt="<?= e(implode(' ', $product['lines'])) ?>" loading="lazy"
            <?php if (!empty($product['tilt'])): ?>style="--tilt: <?= (int) $product['tilt'] ?>deg"<?php endif; ?>>
        <?php if (!empty($product['favorite'])): ?>
            <button class="product-card__fav" type="button" aria-label="Ajouter aux favoris" aria-pressed="false">
                <img src="<?= img('icon-heart-circle.svg') ?>" alt="" width="63" height="63">
            </button>
        <?php endif; ?>
    </div>
    <h3 class="product-card__name">
        <?php foreach ($product['lines'] as $line): ?>
            <span><?= e($line) ?></span>
        <?php endforeach; ?>
    </h3>
    <p class="product-card__price"><?= e($product['price']) ?></p>
    <button class="btn-cart" type="button">AJOUTER AU PANIER</button>
</article>
