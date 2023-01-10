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
 * @method void saveList(EmailVerificationToken[] $token)
 * @method void removeList(EmailVerificationToken[] $token)
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
    private const TOKEN_LENGTH = 88;

    public function __construct(
        EmailVerificationTokenRepository $repository,
        private RandomStringGenerator $tokenGenerator,
    ) {
        parent::__construct($repository);
    }

    /**
     * Create EmailVerificationToken entity with random token field
     */
    public function createRandom(User $user): EmailVerificationToken
    {
        $tokenString = $this->tokenGenerator->generateUriString(self::TOKEN_LENGTH);

        $token = new EmailVerificationToken();

        $token->setOwner($user);
        $token->setToken($tokenString);

        $this->save($token);

        return $token;
    }

    public function findOneByTokenOrNull(string $token): ?EmailVerificationToken
    {
        return $this->getRepository()->findOneByOrNull(['token' => $token]);
    }

    public function findOneByToken(string $token): EmailVerificationToken
    {
        return $this->findOneByTokenOrNull($token) ?? throw new EntityNotFoundException();
    }

    protected function getRepository(): EmailVerificationTokenRepository
    {
        /** @var EmailVerificationTokenRepository */
        return parent::getRepository();
    }
}
