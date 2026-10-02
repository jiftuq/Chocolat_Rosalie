<?php

namespace App\Controller\Api;

use App\Core\JsonResponse;
use App\Core\Session;
use App\Core\Validator;
use App\Exception\TooManyRequestsException;
use App\Manager\ContactMessageManager;
use App\Model\ContactMessage;

/**
 * Formulaire de contact (BE-50). Protection anti-spam :
 *  - champ piège invisible « website » que seuls les robots remplissent ;
 *  - délai minimum de 3 secondes entre l'affichage et l'envoi ;
 *  - 3 messages maximum par session et par tranche de 10 minutes.
 */
class ContactController extends AbstractApiController
{
    private const MIN_SECONDS_BEFORE_SUBMIT = 3;
    private const MAX_MESSAGES = 3;
    private const PERIOD_SECONDS = 600;

    public function store(): void
    {
        $v = new Validator($this->input());
        $name = $v->text('name');
        $email = $v->text('email');
        $subject = $v->text('subject');
        $message = $v->multiline('message');

        $v->required('name', $name, 'Le nom')
            ->length('name', $name, 2, 100, 'Le nom')
            ->required('email', $email, 'L’adresse e-mail')
            ->maxLength('email', $email, 254, 'L’adresse e-mail')
            ->email('email', $email)
            ->required('subject', $subject, 'Le sujet')
            ->length('subject', $subject, 3, 120, 'Le sujet')
            ->required('message', $message, 'Le message')
            ->length('message', $message, 10, 2000, 'Le message')
            ->validate();

        $isBot = $v->text('website') !== ''
            || time() - (int) Session::get('contact_form_started', 0) < self::MIN_SECONDS_BEFORE_SUBMIT;

        $recentSends = array_filter(
            Session::get('contact_sent', []),
            fn (int $timestamp) => $timestamp > time() - self::PERIOD_SECONDS
        );
        if (count($recentSends) >= self::MAX_MESSAGES) {
            throw new TooManyRequestsException('Vous avez déjà envoyé plusieurs messages. Merci de réessayer un peu plus tard.');
        }

        // un robot reçoit la même réponse, mais rien n'est enregistré
        if (!$isBot) {
            (new ContactMessageManager($this->pdo()))->add(new ContactMessage([
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
            ]));
            $recentSends[] = time();
            Session::set('contact_sent', array_values($recentSends));
        }

        JsonResponse::success(null, 'Merci ' . $name . ' ! Votre message a bien été envoyé, nous vous répondrons rapidement.', 201);
    }
}
