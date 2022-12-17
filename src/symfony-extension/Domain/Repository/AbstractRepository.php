<?php

declare(strict_types=1);

namespace SymfonyExtension\Domain\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @template T of object
 * @template-extends ServiceEntityRepository<T>
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findByIdOrNull(int $id): ?object
    {
        return $this->find($id);
    }
}
