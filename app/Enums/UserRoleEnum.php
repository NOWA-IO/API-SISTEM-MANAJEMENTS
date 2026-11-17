<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case ADMIN = "Admin";
    case EMPLOYEE = "Employee";
    case SELLER = "Seller";
}
