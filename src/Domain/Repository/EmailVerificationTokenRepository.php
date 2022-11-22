<?php

namespace App\Domain\Repository;

use App\Domain\Entity\EmailVerificationToken;
use App\Domain\Entity\User;
use App\Domain\Exception\EntityNotFoundException;
use App\Extension\Domain\Repository\AbstractRepository;
use App\Library\Token\RandomStringGenerator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<EmailVerificationToken>
 *
 * @method EmailVerificationToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailVerificationToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailVerificationToken[]    findAll()
 * @method EmailVerificationToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailVerificationTokenRepository extends AbstractRepository
{
    private RandomStringGenerator $tokenGenerator;

    public function __construct(ManagerRegistry $registry, RandomStringGenerator $tokenGenerator)
    {
        parent::__construct($registry, EmailVerificationToken::class);
        $this->tokenGenerator = $tokenGenerator;
    }

    public function create(User $user): EmailVerificationToken
    {
        $token = new EmailVerificationToken();

        $token->setOwner($user);
        $token->setToken($this->tokenGenerator->generateSimpleToken(88));

        $this->getEntityManager()->persist($token);

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

    /**
     * @throws EntityNotFoundException
     */
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

    /**
     * @throws EntityNotFoundException
     */
    public function findOneByToken(string $token): ?EmailVerificationToken
    {
        $token = $this->findOneByToken($token);

        if ($token === null) {
            throw new EntityNotFoundException();
        }

        return $token;
    }
}
