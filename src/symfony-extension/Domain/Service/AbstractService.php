<?php

declare(strict_types=1);

namespace SymfonyExtension\Domain\Service;

use SymfonyExtension\Domain\Exception\EntityNotFoundException;
use SymfonyExtension\Domain\Repository\AbstractRepository;

/**
 * @template TEntity of object
 *
 * @phpstan-method void save(TEntity $entity)
 * @phpstan-method void remove(TEntity $entity)
 * @phpstan-method void saveList(TEntity[] $entity)
 * @phpstan-method void removeList(TEntity[] $entity)
 *
 * @phpstan-method TEntity|null findByIdOrNull(int $id)
 * @phpstan-method TEntity      findByIdOr(int $id)
 * @phpstan-method TEntity|null findOneByOrNull(array $criteria)
 * @phpstan-method TEntity      findOneBy(array $criteria)
 * @phpstan-method TEntity[]    findAll()
 * @phpstan-method TEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
abstract class AbstractService
{
    /**
     * @phpstan-param AbstractRepository<TEntity> $repository
     */
    public function __construct(
        private AbstractRepository $repository
    ) {
    }

    /**
     * @phpstan-param TEntity $entity
     */
    public function save(object $entity): void
    {
        $this->getRepository()->save($entity);
        $this->getRepository()->flush();
    }

    /**
     * @phpstan-param TEntity[] $entities
     */
    public function saveList(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->repository->save($entity);
        }

        $this->getRepository()->flush();
    }

    /**
     * @phpstan-param TEntity $entity
     */
    public function remove(object $entity): void
    {
        $this->getRepository()->remove($entity);
        $this->getRepository()->flush();
    }

    /**
     * @phpstan-param TEntity[] $entities
     */
    public function removeList(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->repository->remove($entity);
        }

        $this->getRepository()->flush();
    }

    /**
     * @phpstan-return ?TEntity
     */
    public function findByIdOrNull(int $id): ?object
    {
        return $this->getRepository()->findByIdOrNull($id);
    }

    /**
     * @phpstan-return TEntity
     */
    public function findById(int $id): object
    {
        return $this->getRepository()->findByIdOrNull($id) ?? throw new EntityNotFoundException();
    }

    /**
     * @param array<string,mixed> $criteria
     *
     * @phpstan-return ?TEntity
     */
    public function findOneByOrNull(array $criteria): ?object
    {
        return $this->getRepository()->findOneByOrNull($criteria);
    }

    /**
     * @param array<string,mixed> $criteria
     *
     * @phpstan-return TEntity
     */
    public function findOneBy(array $criteria): object
    {
        return $this->getRepository()->findOneByOrNull($criteria) ?? throw new EntityNotFoundException();
    }

    /**
     * @phpstan-return TEntity[]
     */
    public function findAll(): array
    {
        return $this->getRepository()->findAll();
    }

    /**
     * @param array<string,mixed> $criteria
     * @param ?array<string,string> $orderBy
     * @param ?int $limit
     * @param ?int $offset
     *
     * @phpstan-return TEntity[]
     */
    public function findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): array
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @phpstan-return AbstractRepository<TEntity>
     */
    protected function getRepository(): AbstractRepository
    {
        return $this->repository;
    }
}
