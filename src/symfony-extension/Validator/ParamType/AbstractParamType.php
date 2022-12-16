<?php

declare(strict_types=1);

namespace SymfonyExtension\Validator\ParamType;

use Symfony\Component\Validator\Constraint;
use SymfonyExtension\Validator\ParamType\ParamTypeConverterInterface;

abstract class AbstractParamType extends Constraint
{
    /**
     * Return class that converts paramter value.
     *
     * @phpstan-return class-string<ParamTypeConverterInterface>
     */
    public function convertedBy(): string
    {
        /** @phpstan-var class-string<ParamTypeConverterInterface>  */
        return $this->validatedBy();
    }
}
