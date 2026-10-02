<?php use App\Core\View; ?>
<section class="page-header">
    <div class="container">
        <h1 class="page-header__title">Contacter Maison Rosalie</h1>
        <p class="page-header__lead">Une question, un projet ou une envie gourmande ? Nous serons ravis de vous répondre.</p>
    </div>
</section>

<section class="section pt-4">
    <div class="container">
        <div class="row g-4 g-lg-5">
            <div class="col-lg-7">
                <form class="contact-form" id="contactForm" novalidate>
                    <h2 class="h3 mb-3">Envoyez-nous un message</h2>
                    <div class="alert d-none" role="alert" data-form-alert></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="contact-name">Nom complet</label>
                            <input class="form-control" type="text" id="contact-name" name="name" autocomplete="name" minlength="2" maxlength="100" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="contact-email">Adresse e-mail</label>
                            <input class="form-control" type="email" id="contact-email" name="email" autocomplete="email" maxlength="254" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="contact-subject">Sujet</label>
                            <input class="form-control" type="text" id="contact-subject" name="subject" minlength="3" maxlength="120" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="contact-message">Message</label>
                            <textarea class="form-control" id="contact-message" name="message" rows="6" minlength="10" maxlength="2000" required></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <!-- champ piège anti-spam : invisible pour les humains -->
                        <div class="hp-field" aria-hidden="true">
                            <label for="contact-website">Ne pas remplir</label>
                            <input type="text" id="contact-website" name="website" tabindex="-1" autocomplete="off">
                        </div>
                        <div class="col-12">
                            <button class="btn btn-brown" type="submit">Envoyer le message</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-5">
                <ul class="contact-infos">
                    <li>
                        <img src="<?= View::asset('img/icon-map.svg') ?>" alt="" width="40" height="40">
                        <address class="mb-0">Rue Saint-Gilles 58<br>4000 Liège, Belgique</address>
                    </li>
                    <li>
                        <img src="<?= View::asset('img/icon-mail.svg') ?>" alt="" width="40" height="40">
                        <a href="mailto:contact@maisonrosalie.be">contact@maisonrosalie.be</a>
                    </li>
                    <li>
                        <img src="<?= View::asset('img/icon-phone.svg') ?>" alt="" width="40" height="40">
                        <a href="tel:+3241234567">+32 4 123 45 67</a>
                    </li>
                </ul>
                <div class="ratio ratio-4x3 contact-map">
                    <iframe src="https://www.google.com/maps?q=Rue%20Saint-Gilles%2058%2C%204000%20Li%C3%A8ge&amp;output=embed"
                            title="Plan d’accès à la Maison Rosalie, Liège" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>
