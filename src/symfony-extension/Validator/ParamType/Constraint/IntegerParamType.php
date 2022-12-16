<?php

declare(strict_types=1);

namespace SymfonyExtension\Validator\ParamType\Constraint;

use SymfonyExtension\Validator\ParamType\AbstractParamType;

class IntegerParamType extends AbstractParamType
{
    public string $message = 'This value should be an integer.';

    public function __construct(string $message = null)
    {
        parent::__construct();

        $this->message = $message ?? $this->message;
    }
}
