<section class="account">
    <div class="account__main">
        <h1 class="account__title account__title--sans">Créer un compte</h1>
        <form class="account__form account__form--register" method="post" action="<?= url('inscription') ?>">
            <label class="field-label" for="reg-firstname">Prénom*</label>
            <input class="field field--line" id="reg-firstname" name="firstname" type="text" autocomplete="given-name" maxlength="50" required>
            <label class="field-label" for="reg-lastname">Nom de famille*</label>
            <input class="field field--line" id="reg-lastname" name="lastname" type="text" autocomplete="family-name" maxlength="50" required>
            <label class="field-label" for="reg-email">Adresse e-mail*</label>
            <input class="field field--line" id="reg-email" name="email" type="email" autocomplete="email" maxlength="254" required>
            <label class="field-label" for="reg-password">Mot de passe*</label>
            <input class="field field--line" id="reg-password" name="password" type="password" autocomplete="new-password" minlength="8" required>
            <label class="field-label" for="reg-birthday">Date d’anniversaire*</label>
            <input class="field field--line" id="reg-birthday" name="birthday" type="date" autocomplete="bday" required>
            <button class="btn-square btn-square--brown" type="submit">CRÉER UN COMPTE</button>
        </form>
    </div>
    <aside class="account__aside account__aside--top">
        <h2 class="account__subtitle account__subtitle--sm">Vous avez déjà un compte?</h2>
        <a class="btn-square btn-square--orange" href="<?= url('connexion') ?>">LOGIN</a>
    </aside>
</section>
