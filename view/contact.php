<section class="contact">
    <h1 class="page-title page-title--left">Contacter Maison Rosalie</h1>

    <div class="contact__intro">
        <p class="contact__question">Une question, un projet<br>ou une envie gourmande ?</p>
        <img class="contact__divider" src="<?= img('line-vertical.svg') ?>" alt="" width="62" height="1">
        <p class="contact__answer">Nous serons ravis de vous répondre et de partager avec vous l’univers de Maison Rosalie</p>
    </div>

    <ul class="contact__infos">
        <li>
            <img src="<?= img('icon-mail.svg') ?>" alt="" width="60" height="60">
            <a href="mailto:contact@maisonrosalie.be">contact@maisonrosalie.be</a>
        </li>
        <li>
            <img src="<?= img('icon-phone.svg') ?>" alt="" width="60" height="60">
            <a href="tel:+3221234567">+32 2 123 45 67</a>
        </li>
        <li>
            <img src="<?= img('icon-map.svg') ?>" alt="" width="60" height="60">
            <span>Rue des Pâtissiers 12<br>1000 Bruxelles, Belgique</span>
        </li>
    </ul>

    <div class="contact__body">
        <form class="contact__form" method="post" action="<?= url('contact') ?>">
            <h2 class="contact__form-title">Envoyez-nous un message</h2>
            <label class="sr-only" for="contact-name">Nom complet</label>
            <input class="field field--boxed" id="contact-name" name="name" type="text" placeholder="Nom complet *" maxlength="100" required>
            <label class="sr-only" for="contact-email">Adresse e-mail</label>
            <input class="field field--boxed" id="contact-email" name="email" type="email" placeholder="Adresse e-mail *" maxlength="254" required>
            <label class="sr-only" for="contact-message">Votre message</label>
            <textarea class="field field--boxed field--area" id="contact-message" name="message" placeholder="Votre message *" maxlength="2000" required></textarea>
            <button class="btn-pill btn-pill--sm" type="submit">ENVOYER LE MESSAGE</button>
        </form>

        <div class="contact__map">
            <img src="<?= img('brussels-map.png') ?>" alt="Plan d’accès : Bruxelles" width="837" height="517" loading="lazy">
            <img class="contact__pin" src="<?= img('icon-map.svg') ?>" alt="" width="60" height="60">
        </div>
    </div>
</section>
