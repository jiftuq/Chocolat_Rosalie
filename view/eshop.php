<section class="shop-intro">
    <h1 class="shop-intro__title">E-shop</h1>
    <p>Achetez vos chocolats belge préférés !</p>
    <p>Découvrez nos création Maison Rosalie et nos coffrets pour vous faire plaisir mais aussi faire plaisir a vos proches.</p>
</section>

<nav class="shop-categories" aria-label="Catégories">
    <?php foreach ($categories as $category): ?>
        <a class="shop-category <?= e($category['class']) ?>" href="#<?= e($category['anchor']) ?>">
            <span class="shop-category__title"><?= nl2br(e($category['title'])) ?></span>
            <span class="shop-category__img"><img src="<?= img($category['image']) ?>" alt="" loading="lazy"></span>
        </a>
    <?php endforeach; ?>
</nav>

<section class="shop-banner">
    <p>Achetez vos chocolats belge préférés !</p>
    <p>Découvrez nos création Maison Rosalie et nos coffrets pour vous faire plaisir mais aussi faire plaisir a vos proches.</p>
</section>
<img class="shop-banner__img" src="<?= img('shop/banner.jpg') ?>" alt="La collection Maison Rosalie" width="1462" height="487" loading="lazy">
<p class="shop-label">Produit</p>

<?php foreach ($sections as $section): ?>
    <section class="shop-section <?= e(preg_replace('/^(\S+)/', 'shop-section--$1', $section['layout'])) ?>" id="<?= e($section['id']) ?>">
        <?php if ($section['title'] !== ''): ?>
            <h2 class="shop-section__title"><?= nl2br(e($section['title'])) ?></h2>
        <?php endif; ?>
        <div class="shop-section__grid">
            <?php foreach ($section['products'] as $product): ?>
                <?php require ROOT . '/view/partials/productCard.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endforeach; ?>

<script>
    document.querySelectorAll('.product-card__fav').forEach(function (button) {
        button.addEventListener('click', function () {
            button.setAttribute('aria-pressed', button.getAttribute('aria-pressed') === 'true' ? 'false' : 'true');
        });
    });
</script>
