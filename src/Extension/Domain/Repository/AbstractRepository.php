<?php

declare(strict_types=1);

namespace App\Extension\Domain\Repository;

use App\Domain\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findByIdOrNull(int $id): object
    {
        return $this->find($id);
    }

    public function findById(int $id): object
    {
        $entity = $this->findByIdOrNull($id);

        if ($entity === null) {
            throw new EntityNotFoundException();
        }

        return $entity;
    }
}
