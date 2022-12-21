<?php

declare(strict_types=1);

namespace SymfonyExtension\Validator\ParamType;

use Library\Exceptions\UnexpectedTypeException;

class ParamTypeConverterFactory
{
    public function getInstance(AbstractParamType $constraint): ParamTypeConverterInterface
    {
        $className = $constraint->convertedBy();

        if (!class_exists($className)) {
            throw new UnexpectedTypeException();
        }

        /** @phpstan-var class-string[] */
        $implementedInterfaces = class_implements($className);

        if (!in_array(ParamTypeConverterInterface::class, $implementedInterfaces)) {
            throw new UnexpectedTypeException();
        }

        /** @var ParamTypeConverterInterface */
        $converter = new $className(); // ???  O_o

        return $converter;
    }
}
