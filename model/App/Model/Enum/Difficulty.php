<?php

namespace App\Model\Enum;

/** Difficulté d'une recette, telle que stockée dans recipes.difficulty. */
enum Difficulty: string
{
    case Easy = 'easy';
    case Medium = 'medium';
    case Hard = 'hard';

    public function label(): string
    {
        return match ($this) {
            self::Easy => 'Facile',
            self::Medium => 'Moyen',
            self::Hard => 'Difficile',
        };
    }

    /** Niveau de 1 à 3, utilisé pour l'affichage visuel (toques). */
    public function level(): int
    {
        return match ($this) {
            self::Easy => 1,
            self::Medium => 2,
            self::Hard => 3,
        };
    }
}
