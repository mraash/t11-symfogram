<?php

declare(strict_types=1);

namespace SymfonyExtension\Validator\ParamType\Constraint;

use SymfonyExtension\Validator\ParamType\AbstractParamType;

class FileParamType extends AbstractParamType
{
    public string $message = 'Request is invalid.';

    public function __construct(string $message = null)
    {
        parent::__construct();

        $this->message = $message ?? $this->message;
    }
}
