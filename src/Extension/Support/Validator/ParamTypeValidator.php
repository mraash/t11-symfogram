<?php

declare(strict_types=1);

namespace App\Extension\Support\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @see ParamType note
 */
class ParamTypeValidator extends ConstraintValidator
{
    private array $types = [
        // ...
        'int' => [
            // ...
            'stringChecker' => 'isIntegerString',
            // ...
            'converter' => 'toInteger',
            // ...
            'checker' => 'isInteger',
            // ...
            'nameInMessage' => 'integer'
        ],
        'float' => [
            'stringChecker' => 'isFloatString',
            'converter' => 'toFloat',
            'checker' => 'isFloat',
            'nameInMessage' => 'float'
        ],
        'number' => [
            'stringChecker' => 'isNumberString',
            'converter' => 'toNumber',
            'checker' => 'isNumber',
            'nameInMessage' => 'number'
        ],
        'checkbox' => [
            'stringChecker' => 'isCheckboxString',
            'converter' => 'toCheckbox',
            'checker' => 'isCheckbox',
            'nameInMessage' => 'checkbox'
        ],
    ];

    /**
     * If the value is convertible, method will return converted value. Otherwise unchanged value.
     *  Example: If type is integer and value is '123' - the integer 123 will be returned. If type 
     *  is integer and value is 'abc' - 'abc' string will be returned.
     *
     * @param string $type First argument of ParamType constraint.
     *
     * @param ?string $value Parameter value (string) or null if it is not present.
     */
    public function convertIfPossible(string $type, ?string $value): mixed
    {
        if (!isset($this->types[$type])) {
            $allowedTypes = implode(', ', array_keys($this->types));

            throw new ConstraintDefinitionException(
                "Invalid type argument. It can be only {$allowedTypes}. {$type} given."
            );
        }

        $checker = $this->types[$type]['stringChecker'];
        $converter = $this->types[$type]['converter'];

        if (!$this->$checker($value)) {
            return $value;
        }

        return $this->$converter($value);
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof ParamType)) {
            throw new UnexpectedTypeException($constraint, ParamType::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!isset($this->types[$constraint->type])) {
            $allowedTypes = implode(', ', array_keys($this->types));

            throw new ConstraintDefinitionException(
                "Invalid ParamType type argument. It can be only {$allowedTypes}. {$constraint->type} given."
            );
        }

        $checker = $this->types[$constraint->type]['checker'];
        $expected = $this->types[$constraint->type]['nameInMessage'];

        if (!$this->$checker($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ expected }}', $expected)
                ->addViolation()
            ;
        }
    }

    // Integer
    private function isIntegerString(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        return preg_match('/^[0-9]+$/', $value) === 1;
    }

    private function toInteger(string $value): int
    {
        return (int)$value;
    }

    private function isInteger(mixed $value): bool
    {
        return is_int($value);
    }

    // Float
    private function isFloatString(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        return preg_match('/^[0-9]+\.[0-9]+$/', $value) === 1;
    }

    private function toFloat(string $value): float
    {
        return (float)$value;
    }

    private function isFloat(mixed $value): bool
    {
        return is_float($value);
    }

    // Number
    private function isNumberString(?string $value): bool
    {
        return $this->isIntegerString($value) || $this->isFloatString($value);
    }

    private function toNumber(string $value): int|float
    {
        return $value + 0;
    }

    private function isNumber(mixed $value): bool
    {
        return $this->isInteger($value) || $this->isFloat($value);
    }

    // Checkbox
    private function isCheckboxString(?string $value): bool
    {
        return $value === 'on' || $value === null;
    }

    private function toCheckbox(?string $value): bool
    {
        return $value !== null;
    }

    private function isCheckbox(mixed $value): bool
    {
        return is_bool($value);
    }
}
