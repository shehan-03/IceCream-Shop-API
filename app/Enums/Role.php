<?php

namespace App\Enums;

enum Role: string
{
    case Owner = 'owner';
    case Manager = 'manager';    // fixed staff roles, stored in users.role
    case Cashier = 'cashier';
}
