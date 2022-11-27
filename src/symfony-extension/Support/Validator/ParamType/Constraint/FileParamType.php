<?php

declare(strict_types=1);

namespace SymfonyExtension\Support\Validator\ParamType\Constraint;

use SymfonyExtension\Support\Validator\ParamType\AbstractParamType;

class FileParamType extends AbstractParamType
{
    public string $message = 'This value should be a file.';

    public function __construct(string $message = null)
    {
        parent::__construct();

        $this->message = $message ?? $this->message;
    }
}
