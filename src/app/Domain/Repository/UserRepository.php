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

    public function create(string $email, string $password): User
    {
        $user = new User();

        $user->setEmail($email);
        $user->setPassword($password);

        $this->getEntityManager()->persist($user);

        return $user;
    }

    public function findOneByEmailOrNull(string $email): ?User
    {
        /** @var ?User */
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->setParameter('val', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
