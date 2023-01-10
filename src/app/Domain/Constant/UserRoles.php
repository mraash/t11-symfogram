<?php

declare(strict_types=1);

namespace App\Domain\Constant;

enum UserRoles: string
{
    case Created = 'ROLE_CREATED';
    case Verified = 'ROLE_VERIFIED';
    case Based = 'ROLE_BASED';
}
