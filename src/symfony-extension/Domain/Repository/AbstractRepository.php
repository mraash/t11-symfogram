<?php

declare(strict_types=1);

namespace SymfonyExtension\Domain\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use RuntimeException;

/**
 * @template TEntity of object
 * @template-extends ServiceEntityRepository<TEntity>
 *
 * @phpstan-method void save(TEntity $entity)
 * @phpstan-method void remove(TEntity $entity)
 *
 * @phpstan-method TEntity|null findByIdOrNull(int $id)
 * @phpstan-method TEntity|null findOneByOrNull(array $criteria)
 * @phpstan-method TEntity[]    findAll()
 * @phpstan-method TEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @phpstan-param TEntity $entity
     */
    public function save(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     * @phpstan-param TEntity $entity
     */
    public function remove(object $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * @phpstan-return ?TEntity $entity
     */
    public function findByIdOrNull(int $id): ?object
    {
        return parent::find($id);
    }

    /**
     * @param array<string,mixed> $criteria
     *
     * @phpstan-return ?TEntity $entity
     */
    public function findOneByOrNull(array $criteria): ?object
    {
        return parent::findOneBy($criteria);
    }
}
