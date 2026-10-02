<?php

namespace App\Model;

use App\Model\Enum\Difficulty;

class Recipe extends AbstractModel
{
    private ?int $id = null;
    private string $title = '';
    private string $slug = '';
    private string $description = '';
    private string $mainImage = '';
    private int $prepTimeMinutes = 0;
    private int $cookTimeMinutes = 0;
    private int $portions = 1;
    private Difficulty $difficulty = Difficulty::Easy;
    private ?string $createdAt = null;

    // Données calculées par les requêtes (jamais stockées à la main, BE-23)
    private ?float $averageRating = null;
    private int $ratingCount = 0;
    /** @var string[] */
    private array $categoryNames = [];
    /** @var RecipeIngredient[] */
    private array $ingredients = [];
    /** @var Step[] */
    private array $steps = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getMainImage(): string
    {
        return $this->mainImage;
    }

    public function getPrepTimeMinutes(): int
    {
        return $this->prepTimeMinutes;
    }

    public function getCookTimeMinutes(): int
    {
        return $this->cookTimeMinutes;
    }

    /** Temps total calculé par le backend, jamais saisi (BE-14). */
    public function getTotalTimeMinutes(): int
    {
        return $this->prepTimeMinutes + $this->cookTimeMinutes;
    }

    public function getPortions(): int
    {
        return $this->portions;
    }

    public function getDifficulty(): Difficulty
    {
        return $this->difficulty;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getAverageRating(): ?float
    {
        return $this->averageRating;
    }

    public function getRatingCount(): int
    {
        return $this->ratingCount;
    }

    /** @return string[] */
    public function getCategoryNames(): array
    {
        return $this->categoryNames;
    }

    /** @return RecipeIngredient[] */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    /** @return Step[] */
    public function getSteps(): array
    {
        return $this->steps;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setTitle(string $title): void
    {
        $this->title = trim($title);
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function setDescription(string $description): void
    {
        $this->description = trim($description);
    }

    public function setMainImage(string $mainImage): void
    {
        $this->mainImage = $mainImage;
    }

    public function setPrepTimeMinutes(int $minutes): void
    {
        $this->prepTimeMinutes = max(0, $minutes);
    }

    public function setCookTimeMinutes(int $minutes): void
    {
        $this->cookTimeMinutes = max(0, $minutes);
    }

    public function setPortions(int $portions): void
    {
        $this->portions = max(1, $portions);
    }

    public function setDifficulty(Difficulty|string $difficulty): void
    {
        $this->difficulty = $difficulty instanceof Difficulty ? $difficulty : Difficulty::from($difficulty);
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /** Moyenne arrondie à une décimale (BE-22). */
    public function setAverageRating(float|string|null $average): void
    {
        $this->averageRating = $average === null ? null : round((float) $average, 1);
    }

    public function setRatingCount(int $count): void
    {
        $this->ratingCount = $count;
    }

    /** Reçoit la liste séparée par « | » produite par GROUP_CONCAT. */
    public function setCategoryNames(array|string|null $names): void
    {
        if (is_string($names)) {
            $names = $names === '' ? [] : explode('|', $names);
        }
        $this->categoryNames = $names ?? [];
    }

    /** @param RecipeIngredient[] $ingredients */
    public function setIngredients(array $ingredients): void
    {
        $this->ingredients = $ingredients;
    }

    /** @param Step[] $steps */
    public function setSteps(array $steps): void
    {
        $this->steps = $steps;
    }

    /** « 1 h 50 », « 45 min » */
    public static function formatDuration(int $minutes): string
    {
        if ($minutes < 60) {
            return $minutes . ' min';
        }
        $rest = $minutes % 60;
        return intdiv($minutes, 60) . ' h' . ($rest > 0 ? ' ' . str_pad((string) $rest, 2, '0', STR_PAD_LEFT) : '');
    }

    /** Version « carte » envoyée en JSON (listes, top 3). */
    public function toSummaryArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'image' => $this->mainImage,
            'categories' => $this->categoryNames,
            'totalTime' => $this->getTotalTimeMinutes(),
            'totalTimeLabel' => self::formatDuration($this->getTotalTimeMinutes()),
            'difficulty' => $this->difficulty->value,
            'difficultyLabel' => $this->difficulty->label(),
            'difficultyLevel' => $this->difficulty->level(),
            'averageRating' => $this->averageRating,
            'ratingCount' => $this->ratingCount,
        ];
    }

    /** Détail complet (BE-12). */
    public function toDetailArray(?int $userRating): array
    {
        return $this->toSummaryArray() + [
            'description' => $this->description,
            'prepTime' => $this->prepTimeMinutes,
            'cookTime' => $this->cookTimeMinutes,
            'portions' => $this->portions,
            'ingredients' => array_map(fn (RecipeIngredient $i) => $i->toArray(), $this->ingredients),
            'steps' => array_map(fn (Step $s) => $s->toArray(), $this->steps),
            'userRating' => $userRating,
        ];
    }
}
