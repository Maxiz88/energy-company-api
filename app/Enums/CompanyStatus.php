<?php

namespace App\Enums;

enum CompanyStatus: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DUPLICATE = 'duplicate';
}
