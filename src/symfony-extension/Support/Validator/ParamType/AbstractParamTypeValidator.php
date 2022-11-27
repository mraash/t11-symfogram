<?php

declare(strict_types=1);

namespace SymfonyExtension\Support\Validator\ParamType;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\ConstraintValidator;

abstract class AbstractParamTypeValidator extends ConstraintValidator implements ParamTypeConverterInterface
{
    public function convertIfPossible(string|UploadedFile|null $paramValue): mixed
    {
        if (!$this->canConvert($paramValue)) {
            return $paramValue;
        }

        return $this->convert($paramValue);
    }

    abstract protected function canConvert(string|UploadedFile|null $paramValue): bool;

    abstract protected function convert(string|UploadedFile|null $paramValue): mixed;
}
