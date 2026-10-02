<footer class="site-footer">
    <div class="site-footer__inner">
        <a class="site-footer__logo" href="<?= url('accueil') ?>">
            <img src="<?= img('logo-vertical.png') ?>" alt="Maison Rosalie" width="163" height="52">
        </a>
        <div class="site-footer__col">
            <a class="site-footer__title" href="<?= url('recettes') ?>">Nos recettes</a>
            <ul class="site-footer__list site-footer__list--light">
                <?php foreach ($footerRecipes as $recipe): ?>
                    <li><a href="<?= url('recette', ['slug' => $recipe['slug']]) ?>"><?= e($recipe['menu']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="site-footer__col">
            <p class="site-footer__title">Nous contacter</p>
            <ul class="site-footer__list">
                <li><a href="https://www.facebook.com/">Facebook</a></li>
                <li><a href="https://www.instagram.com/">Istagram</a></li>
                <li><a href="<?= url('accueil') ?>">www.MaisonRosalie.be</a></li>
            </ul>
        </div>
        <div class="site-footer__col">
            <p class="site-footer__title">Service Client</p>
            <ul class="site-footer__list">
                <li><a href="<?= url('contact') ?>">Faq</a></li>
                <li><a href="<?= url('contact') ?>">Expédition &amp; livraison</a></li>
                <li><a href="<?= url('a-propos') ?>">Carrière chez Maison Rosalie</a></li>
                <li><a href="<?= url('contact') ?>">Contactez-nous</a></li>
            </ul>
        </div>
        <div class="site-footer__social">
            <a href="https://www.facebook.com/" aria-label="Facebook"><img src="<?= img('facebook.svg') ?>" alt="" width="36" height="41"></a>
            <a href="https://x.com/" aria-label="X"><img src="<?= img('twitter.svg') ?>" alt="" width="35" height="36"></a>
            <a href="https://www.instagram.com/" aria-label="Instagram"><img src="<?= img('instagram.svg') ?>" alt="" width="37" height="37"></a>
        </div>
    </div>
</footer>
