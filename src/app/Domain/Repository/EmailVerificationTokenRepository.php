<?php

namespace App\Domain\Repository;

use App\Domain\Entity\EmailVerificationToken;
use App\Domain\Entity\User;
use SymfonyExtension\Domain\Exception\EntityNotFoundException;
use SymfonyExtension\Domain\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<EmailVerificationToken>
 *
 * @method EmailVerificationToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailVerificationToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailVerificationToken[]    findAll()
 * @method EmailVerificationToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method EmailVerificationToken|null findByIdOrNull(int $id)
 */
class EmailVerificationTokenRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailVerificationToken::class);
    }

    public function create(User $user, string $tokenString): EmailVerificationToken
    {
        $token = new EmailVerificationToken();

        $token->setOwner($user);
        $token->setToken($tokenString);

        $this->save($token);

        return $token;
    }

    public function save(EmailVerificationToken $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function remove(EmailVerificationToken $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    public function findOneByTokenOrNull(string $token): ?EmailVerificationToken
    {
        /** @var ?EmailVerificationToken */
        return $this->createQueryBuilder('t')
           ->andWhere('t.token = :val')
           ->setParameter('val', $token)
           ->getQuery()
           ->getOneOrNullResult()
        ;
    }
}
