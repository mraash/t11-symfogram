<?php

declare(strict_types=1);

namespace SymfonyExtension\Support\Validator\ParamType;

use Symfony\Component\Validator\ConstraintValidator;

abstract class AbstractParamTypeValidator extends ConstraintValidator implements ParamTypeConverterInterface
{
    public function convertIfPossible(string|null $paramValue): mixed
    {
        if (!$this->canConvert($paramValue)) {
            return $paramValue;
        }

        return $this->convert($paramValue);
    }

    abstract protected function canConvert(string|null $paramValue): bool;

    abstract protected function convert(string|null $paramValue): mixed;
}
