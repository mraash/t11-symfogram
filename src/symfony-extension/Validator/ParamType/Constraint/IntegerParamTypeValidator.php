<?php

declare(strict_types=1);

namespace SymfonyExtension\Validator\ParamType\Constraint;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use SymfonyExtension\Validator\ParamType\AbstractParamTypeValidator;

class IntegerParamTypeValidator extends AbstractParamTypeValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof IntegerParamType)) {
            throw new UnexpectedTypeException($constraint, IntegerParamType::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_int($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

    protected function canConvert(string|UploadedFile|null $param): bool
    {
        if (!is_string($param)) {
            return false;
        }

        return preg_match('/^\d+$/', $param) === 1;
    }

    protected function convert(string|UploadedFile|null $param): int
    {
        /** @var string $param */

        return (int)$param;
    }
}
