<?php

namespace App\Manager;

use App\Model\User;

class UserManager extends AbstractManager
{
    public function getById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : new User($row);
    }

    public function getByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => mb_strtolower($email)]);
        $row = $stmt->fetch();
        return $row === false ? null : new User($row);
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM users WHERE email = :email');
        $stmt->execute(['email' => mb_strtolower($email)]);
        return $stmt->fetchColumn() !== false;
    }

    public function usernameExists(string $username): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        return $stmt->fetchColumn() !== false;
    }

    public function add(User $user): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (username, email, password_hash, role)
             VALUES (:username, :email, :hash, :role)'
        );
        $stmt->execute([
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'hash' => $user->getPasswordHash(),
            'role' => $user->getRole()->value,
        ]);
        $user->setId((int) $this->pdo->lastInsertId());
    }
}
