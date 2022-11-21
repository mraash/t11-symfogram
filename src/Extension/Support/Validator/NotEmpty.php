<?php

declare(strict_types=1);

namespace App\Extension\Support\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Checks if the value is an empty string or null.
 */
class NotEmpty extends Constraint
{
    public string $message = 'This value is required.';

    public function __construct(string $message = null)
    {
        parent::__construct();

        if ($message !== null) {
            $this->message = $message;
        }
    }
}