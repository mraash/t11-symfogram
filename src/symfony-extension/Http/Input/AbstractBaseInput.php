<?php

declare(strict_types=1);

namespace SymfonyExtension\Http\Input;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraint;

abstract class AbstractBaseInput extends AbstractInput
{
    protected function rules(): Collection
    {
        return new Collection([
            'allowExtraFields' => true,
            'allowMissingFields' => true,
            'fields' => $this->fields(),
        ]);
    }

    /**
     * @return array<string,Constraint|Constraint[]>
     */
    protected function fields(): array
    {
        return [];
    }
}
