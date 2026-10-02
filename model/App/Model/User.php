<?php

namespace App\Model;

use App\Model\Enum\Role;

class User extends AbstractModel
{
    private ?int $id = null;
    private string $username = '';
    private string $email = '';
    private string $passwordHash = '';
    private Role $role = Role::User;
    private ?string $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setUsername(string $username): void
    {
        $this->username = trim($username);
    }

    public function setEmail(string $email): void
    {
        $this->email = mb_strtolower(trim($email));
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    /** Le mot de passe en clair n'est jamais stocké (SEC-04). */
    public function setPlainPassword(string $password): void
    {
        $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    public function setRole(Role|string $role): void
    {
        $this->role = $role instanceof Role ? $role : Role::from($role);
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
