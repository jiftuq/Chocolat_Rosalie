<?php

namespace App\Exception;

class UnauthorizedException extends HttpException
{
    public function __construct(string $message = "Vous devez être connecté pour effectuer cette action.")
    {
        parent::__construct($message, 401);
    }
}
