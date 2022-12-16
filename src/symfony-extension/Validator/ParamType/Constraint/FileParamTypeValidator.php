<?php

declare(strict_types=1);

namespace SymfonyExtension\Validator\ParamType\Constraint;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use SymfonyExtension\Validator\ParamType\AbstractParamTypeValidator;

class FileParamTypeValidator extends AbstractParamTypeValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof FileParamType)) {
            throw new UnexpectedTypeException($constraint, FileParamType::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!($value instanceof UploadedFile)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

    protected function canConvert(string|UploadedFile|null $param): bool
    {
        return $param instanceof UploadedFile;
    }

    protected function convert(string|UploadedFile|null $param): UploadedFile
    {
        /** @var UploadedFile $param */

        return $param;
    }
}
