<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\EmailVerificationToken;
use App\Domain\Entity\User;
use App\Domain\Repository\EmailVerificationTokenRepository;
use Library\Token\RandomStringGenerator;
use SymfonyExtension\Domain\Exception\EntityNotFoundException;
use SymfonyExtension\Domain\Service\AbstractService;

/**
 * @extends AbstractService<EmailVerificationToken>
 *
 * @method void save(EmailVerificationToken $token)
 * @method void remove(EmailVerificationToken $token)
 *
 * @method EmailVerificationToken|null findByIdOrNull(int $id)
 * @method EmailVerificationToken      findByIdOr(int $id)
 * @method EmailVerificationToken|null findOneByOrNull(array $criteria)
 * @method EmailVerificationToken      findOneBy(array $criteria)
 * @method EmailVerificationToken[]    findAll()
 * @method EmailVerificationToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailVerificationTokenService extends AbstractService
{
    public function __construct(
        EmailVerificationTokenRepository $repository,
        private RandomStringGenerator $tokenGenerator,
    ) {
        parent::__construct($repository);
    }

    public function create(User $user): EmailVerificationToken
    {
        $tokenString = $this->tokenGenerator->generateUriString(88);

        $token = $this->getRepository()->create($user, $tokenString);
        $this->getRepository()->flush();

        return $token;
    }

    public function findOneByTokenOrNull(string $token): ?EmailVerificationToken
    {
        return $this->getRepository()->findOneByTokenOrNull($token);
    }

    public function findOneByToken(string $token): EmailVerificationToken
    {
        return $this->getRepository()->findOneByTokenOrNull($token) ?? throw new EntityNotFoundException();
    }

    protected function getRepository(): EmailVerificationTokenRepository
    {
        /** @var EmailVerificationTokenRepository */
        return parent::getRepository();
    }
}
