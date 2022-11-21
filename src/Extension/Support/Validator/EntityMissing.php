<?php

declare(strict_types=1);

namespace App\Extension\Support\Validator;

use Symfony\Component\Validator\Constraint;

class EntityMissing extends Constraint
{
    public string $message = 'This value is taken.';
    public string $field;
    public string $entityClass;

    public function __construct(string $field, string $entityClass, string $message = null)
    {
        parent::__construct();

        $this->field = $field;
        $this->entityClass = $entityClass;
        $this->message = $message ?? $this->message;
    }
}
