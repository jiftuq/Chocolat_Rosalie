<?php

namespace App\Exception;

class ForbiddenException extends HttpException
{
    public function __construct(string $message = "Vous n'avez pas le droit d'effectuer cette action.")
    {
        parent::__construct($message, 403);
    }
}
