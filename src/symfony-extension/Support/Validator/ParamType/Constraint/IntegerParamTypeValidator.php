<?php

declare(strict_types=1);

namespace SymfonyExtension\Support\Validator\ParamType\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use SymfonyExtension\Support\Validator\ParamType\AbstractParamTypeValidator;

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

    protected function canConvert(string|null $param): bool
    {
        if ($param === null) {
            return false;
        }

        return preg_match('/^\d+$/', $param) === 1;
    }

    protected function convert(string|null $param): int
    {
        return (int)$param;
    }
}
