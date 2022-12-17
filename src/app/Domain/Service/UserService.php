<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyExtension\Domain\Exception\EntityNotFoundException;

class UserService
{
    public function __construct(
        private UserRepository $repository,
        private PostService $postService,
        private PostImageService $postImageService,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function create(string $email, string $plainPassword): User
    {
        $user = new User();

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

        $user->setEmail($email);
        $user->setPassword($hashedPassword);

        $this->repository->save($user);

        return $user;
    }

    public function createAndSetAvatar(User $user, string $avatarUri): void
    {
        $post = $this->postService->create($user);
        $image = $this->postImageService->create($post, $avatarUri);

        $user->setAvatar($image);

        $this->repository->save($user);
        $this->repository->flush();
    }

    public function save(User $user): void
    {
        $this->repository->save($user);
    }

    public function remove(User $user): void
    {
        $this->repository->remove($user);
    }

    /**
     * @return User[]
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function findByIdOrNull(int $id): ?User
    {
        return $this->repository->findByIdOrNull($id);
    }

    public function findById(int $id): User
    {
        $token = $this->findByIdOrNull($id) ?? throw new EntityNotFoundException();

        return $token;
    }

    public function findOneByEmailOrNull(string $email): ?User
    {
        return $this->repository->findOneByEmailOrNull($email);
    }

    public function findOneByEmail(string $email): User
    {
        $user = $this->findOneByEmailOrNull($email) ?? throw new EntityNotFoundException();;

        return $user;
    }
}
