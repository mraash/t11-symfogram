<?php

namespace App\Domain\Repository;

use App\Domain\Entity\EmailVerificationToken;
use App\Domain\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyExtension\Domain\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<EmailVerificationToken>
 *
 * @method void save(EmailVerificationToken $token)
 * @method void remove(EmailVerificationToken $token)
 *
 * @method EmailVerificationToken|null findByIdOrNull(int $id)
 * @method EmailVerificationToken|null findOneByOrNull(array $criteria)
 * @method EmailVerificationToken[]    findAll()
 * @method EmailVerificationToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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
