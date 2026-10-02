<?php

namespace App\Exception;

class NotFoundException extends HttpException
{
    public function __construct(string $message = "Élément introuvable.")
    {
        parent::__construct($message, 404);
    }
}
