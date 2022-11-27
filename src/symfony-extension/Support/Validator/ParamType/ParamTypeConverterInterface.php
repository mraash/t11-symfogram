<?php

declare(strict_types=1);

namespace SymfonyExtension\Support\Validator\ParamType;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ParamTypeConverterInterface
{
    /**
     * If $paramValue is convertible, method will return converted $paramValue. Otherwise unchanged $paramValue.
     *
     *  Example: If type is integer and $paramValue is '123' - the integer 123 will be returned. If type
     *  is integer and $paramValue is 'abc' - 'abc' string will be returned.
     *
     * @param ?string $paramValue Parameter value (string) or null if it is not present.
     */
    public function convertIfPossible(string|UploadedFile|null $paramValue): mixed;
}
