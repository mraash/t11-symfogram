<?php

declare(strict_types=1);

namespace App\Http\SupportService\EmailVerifier\Exceptions;

use Exception;

/**
 * Means that the request does not have a token parameter/attribute.
 */
class TokenNotProvidedException extends Exception
{
}
