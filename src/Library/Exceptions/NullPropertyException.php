<?php

declare(strict_types=1);

namespace Library\Exceptions;

use Exception;

/**
 * An exception for cases where a class has a property of typeX|null, but
 *  its getter method must return only typeX. So if the property is null, the
 *  getter should throw this exception.
 */
class NullPropertyException extends Exception
{
}
