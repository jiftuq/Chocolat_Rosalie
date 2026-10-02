<?php

namespace App\Controller\Api;

use App\Core\JsonResponse;
use App\Core\Session;
use App\Core\Validator;
use App\Exception\TooManyRequestsException;
use App\Exception\UnauthorizedException;
use App\Exception\ValidationException;
use App\Manager\LoginAttemptManager;
use App\Manager\UserManager;
use App\Model\User;
use PDOException;

class AuthController extends AbstractApiController
{
    // Politique de mot de passe (BE-01) : 10 caractères minimum, au moins une lettre et un chiffre
    public const PASSWORD_MIN_LENGTH = 10;
    // bcrypt ignore tout ce qui dépasse 72 octets
    public const PASSWORD_MAX_LENGTH = 72;

    // Freinage des tentatives (BE-03)
    private const MAX_FAILURES_PER_EMAIL = 5;
    private const MAX_FAILURES_PER_IP = 20;
    private const LOCK_MINUTES = 15;

    // empreinte factice pour que le temps de réponse ne révèle pas si un compte existe
    private const TIMING_DUMMY_HASH = '$2y$10$luhCVse89O5JzxD.WPXiuOzSJ473POH1aEyI4Teve/ZBTa/CpRrbC';

    /** Utilisateur actuellement connecté (BE-04). */
    public function me(): void
    {
        JsonResponse::success($this->sessionPayload());
    }

    public function register(): void
    {
        $v = new Validator($this->input());
        $username = $v->text('username');
        $email = mb_strtolower($v->text('email'));
        $password = $v->raw('password');
        $confirm = $v->raw('password_confirm');

        $v->required('username', $username, 'Le nom d’utilisateur')
            ->length('username', $username, 3, 30, 'Le nom d’utilisateur')
            ->pattern('username', $username, '/^[\p{L}0-9_.-]+$/u', 'Lettres, chiffres, points, tirets et « _ » uniquement.')
            ->required('email', $email, 'L’adresse e-mail')
            ->maxLength('email', $email, 254, 'L’adresse e-mail')
            ->email('email', $email)
            ->required('password', $password, 'Le mot de passe')
            ->length('password', $password, self::PASSWORD_MIN_LENGTH, self::PASSWORD_MAX_LENGTH, 'Le mot de passe')
            ->pattern('password', $password, '/(?=.*\p{L})(?=.*\d)/u', 'Le mot de passe doit contenir au moins une lettre et un chiffre.');
        if ($confirm !== $password) {
            $v->addError('password_confirm', 'Les deux mots de passe ne correspondent pas.');
        }

        $users = new UserManager($this->pdo());
        if (!$v->hasErrors()) {
            if ($users->usernameExists($username)) {
                $v->addError('username', 'Ce nom d’utilisateur est déjà utilisé.');
            }
            if ($users->emailExists($email)) {
                $v->addError('email', 'Un compte existe déjà avec cette adresse e-mail.');
            }
        }
        $v->validate();

        $user = new User(['username' => $username, 'email' => $email]);
        $user->setPlainPassword($password);
        try {
            $users->add($user);
        } catch (PDOException $e) {
            // inscription simultanée avec le même nom ou e-mail : la contrainte UNIQUE tranche
            if ($e->getCode() === '23000') {
                throw new ValidationException(['email' => 'Ce nom d’utilisateur ou cet e-mail est déjà utilisé.']);
            }
            throw $e;
        }

        Session::login($user);
        JsonResponse::success($this->sessionPayload(), 'Bienvenue ' . $user->getUsername() . ' ! Votre compte est créé.', 201);
    }

    public function login(): void
    {
        $v = new Validator($this->input());
        $email = mb_strtolower($v->text('email'));
        $password = $v->raw('password');
        $v->required('email', $email, 'L’adresse e-mail')
            ->required('password', $password, 'Le mot de passe')
            ->validate();

        $attempts = new LoginAttemptManager($this->pdo());
        $ip = $this->clientIp();
        if ($attempts->countRecentFailuresByEmail($email, self::LOCK_MINUTES) >= self::MAX_FAILURES_PER_EMAIL
            || $attempts->countRecentFailuresByIp($ip, self::LOCK_MINUTES) >= self::MAX_FAILURES_PER_IP) {
            throw new TooManyRequestsException('Trop de tentatives de connexion. Réessayez dans ' . self::LOCK_MINUTES . ' minutes.');
        }

        $user = (new UserManager($this->pdo()))->getByEmail($email);
        // même temps de calcul que le compte existe ou non
        $valid = $user !== null
            ? $user->verifyPassword($password)
            : password_verify($password, self::TIMING_DUMMY_HASH);
        $attempts->add($email, $ip, $valid);

        if (!$valid) {
            // le message ne dit jamais si c'est l'e-mail ou le mot de passe (BE-02)
            throw new UnauthorizedException('Adresse e-mail ou mot de passe incorrect.');
        }

        Session::login($user);
        JsonResponse::success($this->sessionPayload(), 'Bonjour ' . $user->getUsername() . ' !');
    }

    public function logout(): void
    {
        Session::logout();
        Session::start();
        JsonResponse::success($this->sessionPayload(), 'Vous êtes déconnecté. À bientôt !');
    }

    private function sessionPayload(): array
    {
        $user = Session::user();
        return [
            'user' => $user === null ? null : ['username' => $user['username'], 'isAdmin' => Session::isAdmin()],
            'csrfToken' => Session::csrfToken(),
        ];
    }
}
