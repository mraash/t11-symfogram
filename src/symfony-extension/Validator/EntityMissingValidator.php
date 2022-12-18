<?php

declare(strict_types=1);

namespace SymfonyExtension\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Library\Exceptions\UnexpectedTypeException as LibraryUnexpectedTypeException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use SymfonyExtension\Domain\Repository\AbstractRepository;

class EntityMissingValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof EntityMissing)) {
            throw new UnexpectedTypeException($constraint, EntityMissing::class);
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

        if (!($repository instanceof AbstractRepository)) {
            throw new LibraryUnexpectedTypeException();
        }

        $entity = $repository->findOneByOrNull([$field => $value]);

        if ($entity !== null) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
