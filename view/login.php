<section class="account">
    <div class="account__main">
        <h1 class="account__title">Connexion</h1>
        <p class="account__text">Si c’est votre première connexion depuis la mise à jour de notre site web, veuillez réinitialiser votre mot de passe pour accéder à votre compte.</p>
        <form class="account__form" method="post" action="<?= url('connexion') ?>">
            <label class="field-label" for="login-email">Adresse e-mail*</label>
            <input class="field field--line" id="login-email" name="email" type="email" autocomplete="email" maxlength="254" required>
            <label class="field-label" for="login-password">Mot de passe*</label>
            <input class="field field--line" id="login-password" name="password" type="password" autocomplete="current-password" required>
            <button class="btn-square btn-square--orange" type="submit">LOGIN</button>
        </form>
    </div>
    <aside class="account__aside">
        <h2 class="account__subtitle">Nouveaux clients</h2>
        <p class="account__text">Crée un compte Maison Rosalie pour commencer à profiter des avantages.</p>
        <ul class="account__perks">
            <li>Payements plus rapide des futurs commandes</li>
            <li>-5% de réduction pour les achats en ligne</li>
            <li>Cadeau offert pour ton anniversaire</li>
            <li>Suivre l’état de livraison de toutes tes gourmandise</li>
        </ul>
        <a class="btn-square btn-square--brown" href="<?= url('inscription') ?>">CRÉE UN COMPTE</a>
    </aside>
</section>
