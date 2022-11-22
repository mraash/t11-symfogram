<?php

declare(strict_types=1);

namespace App\Extension\Support\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class EntityExistsValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof EntityExists)) {
            throw new UnexpectedTypeException($constraint, EntityExists::class);
        }

        $entityClass = $constraint->entityClass;
        $field = $constraint->field;

        $isEntityClassCorrect = !$this->entityManager->getMetadataFactory()->isTransient($entityClass);

        if (!$isEntityClassCorrect) {
            throw new ConstraintDefinitionException('$entityClass argument is not an Entity object');
        }

        if ($value === null || $value === '') {
            return;
        }

        $repository = $this->entityManager->getRepository($entityClass);

        $entity = $repository->findOneBy([$field => $value]);

        if ($entity === null) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
