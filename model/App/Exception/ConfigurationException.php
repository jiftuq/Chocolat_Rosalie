<?php

namespace App\Exception;

use RuntimeException;

/**
 * Installation incomplète (config.php absent) : le message ne contient aucun
 * secret, il est donc affiché tel quel pour guider l'installation.
 */
class ConfigurationException extends RuntimeException
{
}
