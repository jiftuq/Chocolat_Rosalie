<?php

namespace App\Exception;

use Exception;

/**
 * Exception « métier » qui porte un code HTTP (BE-71) et un message
 * destiné au visiteur. Les exceptions techniques (PDOException...)
 * ne sont jamais montrées telles quelles.
 */
class HttpException extends Exception
{
    public function __construct(string $message, private int $status = 400)
    {
        parent::__construct($message);
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
