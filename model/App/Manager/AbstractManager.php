<?php

namespace App\Manager;

use PDO;

/**
 * Les managers reçoivent leur connexion (injection de dépendance) :
 * ils ne la créent jamais eux-mêmes.
 */
abstract class AbstractManager
{
    public function __construct(protected PDO $pdo)
    {
    }
}
