<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\User;
use App\Domain\Repository\PostImageRepository;
use App\Domain\Repository\PostRepository;
use App\Domain\Repository\UserRepository;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private PostRepository $postRepository,
        private PostImageRepository $postImageRepository
    ) {   
    }

    public function createAndSetAvatar(User $user, string $avatarUri): void
    {
        $post = $this->postRepository->create($user);
        $image = $this->postImageRepository->create($post, $avatarUri);

        $user->setAvatar($image);

        $this->userRepository->save($user);
        $this->userRepository->flush();
    }
}
