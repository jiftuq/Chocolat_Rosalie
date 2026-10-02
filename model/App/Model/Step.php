<?php

namespace App\Model;

class Step extends AbstractModel
{
    private ?int $id = null;
    private int $stepNumber = 1;
    private string $title = '';
    private string $description = '';
    private string $image = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStepNumber(): int
    {
        return $this->stepNumber;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setStepNumber(int $stepNumber): void
    {
        $this->stepNumber = $stepNumber;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function toArray(): array
    {
        return [
            'number' => $this->stepNumber,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
        ];
    }
}
