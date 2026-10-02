<section class="favorites">
    <h1 class="page-title page-title--left">Mes favoris</h1>
    <?php if ($favorites): ?>
        <div class="favorites__grid">
            <?php foreach ($favorites as $product): ?>
                <?php require ROOT . '/view/partials/productCard.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Vous n’avez pas encore de favoris. <a href="<?= url('eshop') ?>">Découvrez l’e-shop</a>.</p>
    <?php endif; ?>
</section>
