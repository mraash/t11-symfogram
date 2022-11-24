<?php

declare(strict_types=1);

namespace SymfonyExtension\Support\Validator\ParamType\Constraint;

use SymfonyExtension\Support\Validator\ParamType\AbstractParamType;

class IntegerParamType extends AbstractParamType
{
    public string $message = 'This value should be an integer.';

    public function __construct(string $message = null)
    {
        parent::__construct();

        $this->message = $message ?? $this->message;
    }
}
