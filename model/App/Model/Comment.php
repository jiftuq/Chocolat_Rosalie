<?php

namespace App\Model;

class Comment extends AbstractModel
{
    private ?int $id = null;
    private int $authorId = 0;
    private int $recipeId = 0;
    private ?string $subject = null;
    private string $message = '';
    private ?string $createdAt = null;
    private string $authorName = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getRecipeId(): int
    {
        return $this->recipeId;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setAuthorId(int $authorId): void
    {
        $this->authorId = $authorId;
    }

    public function setRecipeId(int $recipeId): void
    {
        $this->recipeId = $recipeId;
    }

    public function setSubject(?string $subject): void
    {
        $this->subject = $subject === '' ? null : $subject;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setAuthorName(string $authorName): void
    {
        $this->authorName = $authorName;
    }

    /**
     * Le texte est renvoyé brut : c'est l'affichage (textContent côté JS,
     * htmlspecialchars côté PHP) qui le rend inoffensif (SEC-02, BE-45).
     */
    public function toArray(bool $canDelete): array
    {
        return [
            'id' => $this->id,
            'author' => $this->authorName,
            'subject' => $this->subject,
            'message' => $this->message,
            'createdAt' => $this->createdAt === null ? null : date(DATE_ATOM, strtotime($this->createdAt)),
            'canDelete' => $canDelete,
        ];
    }
}
