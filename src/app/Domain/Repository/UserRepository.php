<?php

namespace App\Domain\Repository;

use App\Domain\Entity\User;
use SymfonyExtension\Domain\Exception\EntityNotFoundException;
use SymfonyExtension\Domain\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method User|null findByIdOrNull(int $id)
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

    public function save(User $user): void
    {
        $this->getEntityManager()->persist($user);
    }

    public function remove(User $user): void
    {
        $this->getEntityManager()->remove($user);
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
