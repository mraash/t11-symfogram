<?php

declare(strict_types=1);

namespace SymfonyExtension\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotEmptyValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof NotEmpty)) {
            throw new UnexpectedTypeException($constraint, NotEmpty::class);
        }

        if ($this->isValueEmpty($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

    public function isValueEmpty(mixed $value): bool
    {
        return $value === null || $value === '';
    }
}
