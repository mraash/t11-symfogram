<?php

namespace App\Domain\Repository;

use App\Domain\Entity\User;
use App\Domain\Exception\EntityNotFoundException;
use App\Extension\Domain\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @extends AbstractRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends AbstractRepository
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(ManagerRegistry $registry, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($registry, User::class);
        $this->passwordHasher = $passwordHasher;
    }

    public function create(string $email, string $plainPassword): User
    {
        $user = new User();

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

        $user->setEmail($email);
        $user->setPassword($hashedPassword);

        $this->getEntityManager()->persist($user);

        return $user;
    }

    public function save(User $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function remove(User $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    public function findOneByEmailOrNull(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->setParameter('val', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByEmail(string $email): User
    {
        $user = $this->findOneByEmailOrNull($email);

        if ($user === null) {
            throw new EntityNotFoundException();
        }

        return $user;
    }
}
