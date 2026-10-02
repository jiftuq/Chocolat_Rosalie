<?php

namespace App\Manager;

use PDO;

/** Historique des connexions, pour freiner les tentatives répétées (BE-03). */
class LoginAttemptManager extends AbstractManager
{
    public function add(string $email, string $ip, bool $successful): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO login_attempts (attempted_email, ip_address, successful)
             VALUES (:email, :ip, :success)'
        );
        $stmt->execute(['email' => mb_strtolower($email), 'ip' => $ip, 'success' => (int) $successful]);
    }

    public function countRecentFailuresByEmail(string $email, int $minutes): int
    {
        return $this->countRecentFailures('attempted_email', mb_strtolower($email), $minutes);
    }

    public function countRecentFailuresByIp(string $ip, int $minutes): int
    {
        return $this->countRecentFailures('ip_address', $ip, $minutes);
    }

    private function countRecentFailures(string $column, string $value, int $minutes): int
    {
        // $column vient uniquement des deux méthodes ci-dessus, jamais de l'utilisateur
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM login_attempts
              WHERE $column = :value AND successful = 0
                AND attempted_at > (NOW() - INTERVAL :minutes MINUTE)"
        );
        $stmt->bindValue('value', $value);
        $stmt->bindValue('minutes', $minutes, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
