<?php

namespace App\Exception;

class TooManyRequestsException extends HttpException
{
    public function __construct(string $message = "Trop de tentatives, veuillez patienter avant de réessayer.")
    {
        parent::__construct($message, 429);
    }
}
