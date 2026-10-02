<?php use App\Core\View; ?>
<footer class="site-footer mt-auto">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-12 col-md-3">
                <img class="site-footer__logo" src="<?= View::asset('img/logo-vertical.png') ?>" alt="Maison Rosalie" width="200" height="63" loading="lazy">
                <p class="small mt-3 mb-0">Des créations d’exception pour des moments précieux.</p>
            </div>
            <nav class="col-6 col-md-3" aria-label="Plan du site">
                <h2 class="site-footer__title">Navigation</h2>
                <ul class="list-unstyled">
                    <li><a href="<?= View::url('accueil') ?>">Accueil</a></li>
                    <li><a href="<?= View::url('recettes') ?>">Recettes</a></li>
                    <li><a href="<?= View::url('a-propos') ?>">À propos</a></li>
                    <li><a href="<?= View::url('contact') ?>">Contact</a></li>
                </ul>
            </nav>
            <div class="col-6 col-md-3">
                <h2 class="site-footer__title">Nous contacter</h2>
                <address class="mb-0">
                    Rue Saint-Gilles 58<br>4000 Liège, Belgique<br>
                    <a href="mailto:contact@maisonrosalie.be">contact@maisonrosalie.be</a><br>
                    <a href="tel:+3241234567">+32 4 123 45 67</a>
                </address>
            </div>
            <div class="col-12 col-md-3">
                <h2 class="site-footer__title">Suivez-nous</h2>
                <ul class="list-inline site-footer__social">
                    <li class="list-inline-item"><a href="#" aria-label="Facebook (lien fictif)"><img src="<?= View::asset('img/facebook.svg') ?>" alt="" width="32" height="32"></a></li>
                    <li class="list-inline-item"><a href="#" aria-label="X (lien fictif)"><img src="<?= View::asset('img/twitter.svg') ?>" alt="" width="30" height="30"></a></li>
                    <li class="list-inline-item"><a href="#" aria-label="Instagram (lien fictif)"><img src="<?= View::asset('img/instagram.svg') ?>" alt="" width="32" height="32"></a></li>
                </ul>
                <button class="btn btn-link site-footer__legal p-0" type="button" data-bs-toggle="modal" data-bs-target="#legalModal">Mentions légales</button>
            </div>
        </div>
        <p class="site-footer__copy small mb-0">© 2026 Maison Rosalie - Tous droits réservés.</p>
    </div>
</footer>
