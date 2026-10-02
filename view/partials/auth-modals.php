<?php use App\Controller\Api\AuthController; ?>
<div class="modal fade" id="loginModal" role="dialog" tabindex="-1" aria-labelledby="loginModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h4" id="loginModalTitle">Connexion</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form id="loginForm" novalidate>
                <div class="modal-body">
                    <div class="alert alert-danger d-none" role="alert" data-form-alert></div>
                    <div class="mb-3">
                        <label class="form-label" for="login-email">Adresse e-mail</label>
                        <input class="form-control" type="email" id="login-email" name="email" autocomplete="email" maxlength="254" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="login-password">Mot de passe</label>
                        <input class="form-control" type="password" id="login-password" name="password" autocomplete="current-password" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button class="btn btn-link px-0" type="button" data-bs-toggle="modal" data-bs-target="#registerModal">Pas encore de compte ?</button>
                    <button class="btn btn-brown" type="submit">Se connecter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="registerModal" role="dialog" tabindex="-1" aria-labelledby="registerModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h4" id="registerModalTitle">Créer un compte</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form id="registerForm" novalidate>
                <div class="modal-body">
                    <div class="alert alert-danger d-none" role="alert" data-form-alert></div>
                    <div class="mb-3">
                        <label class="form-label" for="register-username">Nom d’utilisateur</label>
                        <input class="form-control" type="text" id="register-username" name="username" autocomplete="username"
                               minlength="3" maxlength="30" data-rule="username" aria-describedby="register-username-help" required>
                        <div class="form-text" id="register-username-help">3 à 30 caractères : lettres, chiffres, « . », « - » ou « _ ».</div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="register-email">Adresse e-mail</label>
                        <input class="form-control" type="email" id="register-email" name="email" autocomplete="email" maxlength="254" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="register-password">Mot de passe</label>
                        <input class="form-control" type="password" id="register-password" name="password" autocomplete="new-password"
                               minlength="<?= AuthController::PASSWORD_MIN_LENGTH ?>" maxlength="<?= AuthController::PASSWORD_MAX_LENGTH ?>"
                               data-rule="password" aria-describedby="register-password-help" required>
                        <div class="form-text" id="register-password-help">Au moins <?= AuthController::PASSWORD_MIN_LENGTH ?> caractères, dont une lettre et un chiffre.</div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="register-password-confirm">Confirmation du mot de passe</label>
                        <input class="form-control" type="password" id="register-password-confirm" name="password_confirm" autocomplete="new-password"
                               data-match="register-password" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button class="btn btn-link px-0" type="button" data-bs-toggle="modal" data-bs-target="#loginModal">Déjà inscrit ?</button>
                    <button class="btn btn-brown" type="submit">Créer mon compte</button>
                </div>
            </form>
        </div>
    </div>
</div>
