<?php

declare(strict_types=1);

namespace App\Extension\Support\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Note: Before actually validating a parameter with this constraint, convert the parameter
 *  with ParamTypeValidator::convertIfPossible() method.
 */
class ParamType extends Constraint
{
    public string $message = 'This value should be of type {{ expected }}.';
    public string $type;

    public function __construct(string $type)
    {
        parent::__construct();

        $this->type = $type;
    }
}
