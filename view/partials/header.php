<header class="site-header">
    <p class="site-header__band">Livraison standard gratuite avec commande à 125 $ vers toutes les destinations avec des températures inférieures à 75ºF.</p>
    <div class="site-header__inner">
        <a class="site-header__logo" href="<?= url('accueil') ?>">
            <img src="<?= img('logo-mr.png') ?>" alt="Maison Rosalie" width="636" height="196">
        </a>
        <div class="site-header__right">
            <div class="site-header__icons">
                <a href="<?= url('connexion') ?>" aria-label="Mon compte">
                    <img src="<?= img('icon-contact.svg') ?>" alt="" width="32" height="32">
                </a>
                <a href="<?= url('favoris') ?>" aria-label="Mes favoris">
                    <img src="<?= img('icon-chocolate.svg') ?>" alt="" width="19" height="27">
                </a>
                <a href="<?= url('contact') ?>" aria-label="Informations et contact">
                    <img src="<?= img('icon-info.svg') ?>" alt="" width="29" height="29">
                </a>
            </div>
            <nav class="site-nav" aria-label="Navigation principale">
                <a href="<?= url('recettes') ?>">Recette</a>
                <a href="<?= url('eshop') ?>">E-shop</a>
                <a href="<?= url('a-propos') ?>">À propos</a>
            </nav>
        </div>
    </div>
</header>
