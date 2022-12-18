<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyExtension\Domain\Exception\EntityNotFoundException;
use SymfonyExtension\Domain\Service\AbstractService;

/**
 * @extends AbstractService<User>
 *
 * @method void save(User $user)
 * @method void remove(User $user)
 *
 * @method User|null findByIdOrNull(int $id)
 * @method User      findByIdOr(int $id)
 * @method User|null findOneByOrNull(array $criteria)
 * @method User      findOneBy(array $criteria)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserService extends AbstractService
{
    public function __construct(
        UserRepository $repository,
        private PostService $postService,
        private PostImageService $postImageService,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct($repository);
    }

    public function create(string $email, string $plainPassword): User
    {
        $user = new User();

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

        $user->setEmail($email);
        $user->setPassword($hashedPassword);

        $this->getRepository()->save($user);

        return $user;
    }

    public function createAndSetAvatar(User $user, string $avatarUri): void
    {
        $post = $this->postService->create($user);
        $image = $this->postImageService->create($post, $avatarUri);

        $user->setAvatar($image);

        $this->getRepository()->save($user);
        $this->getRepository()->flush();
    }

    public function findOneByEmailOrNull(string $email): ?User
    {
        return $this->getRepository()->findOneByEmailOrNull($email);
    }

    public function findOneByEmail(string $email): User
    {
        return $this->getRepository()->findOneByEmailOrNull($email) ?? throw new EntityNotFoundException();
    }

    protected function getRepository(): UserRepository
    {
        /** @var UserRepository */
        return parent::getRepository();
    }
}
