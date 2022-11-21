<?php

declare(strict_types=1);

namespace App\Extension\Http\Input;

use Symfony\Component\Validator\Constraints\Collection;

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

    protected function fields(): array
    {
        return [];
    }
}
