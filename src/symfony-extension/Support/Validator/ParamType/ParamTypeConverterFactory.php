<?php

declare(strict_types=1);

namespace SymfonyExtension\Support\Validator\ParamType;

class ParamTypeConverterFactory
{
    public function getInstance(AbstractParamType $constraint): ParamTypeConverterInterface
    {
        $className = $constraint->convertedBy();

        if (!class_exists($className)) {
            throw new \Exception();
        }

        /** @phpstan-var class-string[] */
        $implementedInterfaces = class_implements($className);

        if (!in_array(ParamTypeConverterInterface::class, $implementedInterfaces)) {
            throw new \Exception();
        }

        /** @var ParamTypeConverterInterface */
        $converter = new $className; // ???  O_o

        return $converter;
    }
}
