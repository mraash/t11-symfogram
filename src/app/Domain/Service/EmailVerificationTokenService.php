<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\EmailVerificationToken;
use App\Domain\Entity\User;
use App\Domain\Repository\EmailVerificationTokenRepository;
use Library\Token\RandomStringGenerator;
use SymfonyExtension\Domain\Exception\EntityNotFoundException;

class EmailVerificationTokenService
{
    public function __construct(
        private EmailVerificationTokenRepository $repository,
        private RandomStringGenerator $tokenGenerator,
    ) {
    }

    public function create(User $user): EmailVerificationToken
    {
        $tokenString = $this->tokenGenerator->generateUriString(88);

        $token = $this->repository->create($user, $tokenString);
        $this->repository->flush();

        return $token;
    }

    public function save(EmailVerificationToken $token): void
    {
        $this->repository->save($token);
        $this->repository->flush();
    }

    public function remove(EmailVerificationToken $token): void
    {
        $this->repository->remove($token);
        $this->repository->flush();
    }

    public function findByIdOrNull(int $id): ?EmailVerificationToken
    {
        return $this->repository->findByIdOrNull($id);
    }

    public function findById(int $id): EmailVerificationToken
    {
        $token = $this->findByIdOrNull($id) ?? throw new EntityNotFoundException();

        return $token;
    }

    public function findOneByTokenOrNull(string $token): ?EmailVerificationToken
    {
        return $this->repository->findOneByTokenOrNull($token);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function findOneByToken(string $token): EmailVerificationToken
    {
        $token = $this->findOneByTokenOrNull($token) ?? throw new EntityNotFoundException();

        return $token;
    }
}
