<?php

declare(strict_types=1);

namespace SymfonyExtension\Validator\ParamType\Constraint;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use SymfonyExtension\Validator\ParamType\AbstractParamTypeValidator;

class ArrayParamTypeValidator extends AbstractParamTypeValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof ArrayParamType)) {
            throw new UnexpectedTypeException($constraint, ArrayParamType::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_array($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

    protected function canConvert(mixed $paramValue): bool
    {
        return false;
    }

    protected function convert(mixed $paramValue): mixed
    {
        return $paramValue;
    }
}
