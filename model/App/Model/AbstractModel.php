<?php

namespace App\Model;

/**
 * Hydratation automatique à partir d'un tableau associatif
 * (typiquement une ligne renvoyée par PDO) : la colonne
 * prep_time_minutes appelle le setter setPrepTimeMinutes().
 */
abstract class AbstractModel
{
    public function __construct(array $tab = [])
    {
        $this->hydrate($tab);
    }

    protected function hydrate(array $assoc): void
    {
        foreach ($assoc as $clef => $valeur) {
            $methodeName = 'set' . str_replace('_', '', ucwords($clef, '_'));
            if (method_exists($this, $methodeName)) {
                $this->$methodeName($valeur);
            }
        }
    }
}
