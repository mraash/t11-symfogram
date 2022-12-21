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
}
