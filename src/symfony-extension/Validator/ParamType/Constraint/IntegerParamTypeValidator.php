<?php

declare(strict_types=1);

namespace SymfonyExtension\Validator\ParamType\Constraint;

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

    protected function canConvert(mixed $paramValue): bool
    {
        if (!is_string($paramValue)) {
            return false;
        }

        return preg_match('/^\d+$/', $paramValue) === 1;
    }

    protected function convert(mixed $paramValue): int
    {
        /** @var string $paramValue */

        return (int)$paramValue;
    }
}
