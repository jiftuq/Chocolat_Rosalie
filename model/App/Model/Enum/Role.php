<?php

namespace App\Model\Enum;

enum Role: string
{
    case User = 'user';
    case Admin = 'admin';
}
