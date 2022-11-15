<?php

declare(strict_types=1);

namespace App\Http\SupportService\EmailVerifier\Exceptions;

use Exception;

/**
 * Token not found in db.
 */
class TokenNotFoundException extends Exception
{
}
