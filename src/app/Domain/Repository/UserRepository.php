<?php

namespace App\Domain\Repository;

use App\Domain\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyExtension\Domain\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<User>
 *
 * @method void save(User $user)
 * @method void remove(User $user)
 *
 * @method User|null findByIdOrNull(int $id)
 * @method User|null findOneByOrNull(array $criteria)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return User[]
     */
    public function findAllWithRole(string $role): array
    {
        $role = mb_strtoupper($role);

        /** @var User[] */
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_' . $role . '"%')
            ->getQuery()
            ->getResult()
        ;
    }
}
