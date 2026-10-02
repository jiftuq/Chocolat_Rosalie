<?php

namespace App\Model;

/** Un ingrédient tel qu'utilisé dans une recette (avec sa quantité). */
class RecipeIngredient extends AbstractModel
{
    private string $name = '';
    private ?float $quantity = null;
    private ?string $unit = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    /** « 250 g », « 2 pièces », ou « une pincée » quand la quantité est absente. */
    public function getDisplayQuantity(): string
    {
        if ($this->quantity === null) {
            return $this->unit ?? '';
        }
        $number = rtrim(rtrim(number_format($this->quantity, 3, ',', ' '), '0'), ',');
        return trim($number . ' ' . ($this->unit ?? ''));
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setQuantity(float|string|null $quantity): void
    {
        $this->quantity = $quantity === null ? null : (float) $quantity;
    }

    public function setUnit(?string $unit): void
    {
        $this->unit = $unit;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'display' => $this->getDisplayQuantity(),
        ];
    }
}
