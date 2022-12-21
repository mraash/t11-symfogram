<?php

declare(strict_types=1);

namespace SymfonyExtension\Validator\ParamType;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\ConstraintValidator;

abstract class AbstractParamTypeValidator extends ConstraintValidator implements ParamTypeConverterInterface
{
    public function convertIfPossible(mixed $paramValue): mixed
    {
        if (!$this->canConvert($paramValue)) {
            return $paramValue;
        }

        return $this->convert($paramValue);
    }

    abstract protected function canConvert(mixed $paramValue): bool;

    abstract protected function convert(mixed $paramValue): mixed;
}
