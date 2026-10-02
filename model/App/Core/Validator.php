<?php

namespace App\Core;

use App\Exception\ValidationException;

/**
 * Validation côté serveur de toute donnée reçue (SEC-07, BE-72) :
 * présence, type, longueur, plage. Les erreurs sont collectées par champ.
 */
final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    public function __construct(private array $input)
    {
    }

    /** Texte nettoyé : espaces superflus retirés (BE-41). */
    public function text(string $field): string
    {
        $value = $this->input[$field] ?? '';
        if (!is_string($value)) {
            return '';
        }
        return trim(preg_replace('/\s+/u', ' ', $value) ?? '');
    }

    /** Texte multi-ligne : on conserve les retours à la ligne. */
    public function multiline(string $field): string
    {
        $value = $this->input[$field] ?? '';
        if (!is_string($value)) {
            return '';
        }
        $value = preg_replace("/[ \t]+/u", ' ', str_replace("\r\n", "\n", $value)) ?? '';
        return trim(preg_replace("/\n{3,}/", "\n\n", $value) ?? '');
    }

    public function raw(string $field): string
    {
        $value = $this->input[$field] ?? '';
        return is_string($value) ? $value : '';
    }

    public function required(string $field, string $value, string $label): self
    {
        if ($value === '') {
            $this->addError($field, "$label est obligatoire.");
        }
        return $this;
    }

    public function length(string $field, string $value, int $min, int $max, string $label): self
    {
        $length = mb_strlen($value);
        if ($value !== '' && ($length < $min || $length > $max)) {
            $this->addError($field, "$label doit contenir entre $min et $max caractères.");
        }
        return $this;
    }

    public function maxLength(string $field, string $value, int $max, string $label): self
    {
        if (mb_strlen($value) > $max) {
            $this->addError($field, "$label ne peut pas dépasser $max caractères.");
        }
        return $this;
    }

    public function email(string $field, string $value): self
    {
        if ($value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($field, "L'adresse e-mail n'est pas valide.");
        }
        return $this;
    }

    public function pattern(string $field, string $value, string $regex, string $message): self
    {
        if ($value !== '' && preg_match($regex, $value) !== 1) {
            $this->addError($field, $message);
        }
        return $this;
    }

    /** Entier strict dans une plage : "4" accepté, "4.5", "abc" ou 6 refusés. */
    public function intInRange(string $field, int $min, int $max, string $message): ?int
    {
        $value = $this->input[$field] ?? null;
        $int = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min, 'max_range' => $max]]);
        if ($int === false || is_bool($value) || is_float($value)) {
            $this->addError($field, $message);
            return null;
        }
        return $int;
    }

    public function addError(string $field, string $message): self
    {
        $this->errors[$field] ??= $message;
        return $this;
    }

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }

    /** @throws ValidationException */
    public function validate(): void
    {
        if ($this->hasErrors()) {
            throw new ValidationException($this->errors);
        }
    }
}
