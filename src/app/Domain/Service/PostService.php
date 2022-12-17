<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Post;
use App\Domain\Entity\User;
use App\Domain\Repository\PostRepository;
use SymfonyExtension\Domain\Exception\EntityNotFoundException;

class PostService
{
    public function __construct(
        private PostRepository $repository
    ) {
    }

    public function create(User $user): Post
    {
        $post = $this->repository->create($user);
        $this->repository->flush();

        return $post;
    }

    public function save(Post $post): void
    {
        $this->repository->save($post);
        $this->repository->flush();
    }

    public function remove(Post $post): void
    {
        $this->repository->remove($post);
        $this->repository->flush();
    }

    public function findByIdOrNull(int $id): ?Post
    {
        return $this->repository->findByIdOrNull($id);
    }

    public function findById(int $id): Post
    {
        $post = $this->repository->findByIdOrNull($id) ?? throw new EntityNotFoundException();

        return $post;
    }
}
