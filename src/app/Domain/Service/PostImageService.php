<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Post;
use App\Domain\Entity\PostImage;
use App\Domain\Repository\PostImageRepository;
use SymfonyExtension\Domain\Exception\EntityNotFoundException;

class PostImageService
{
    public function __construct(
        private PostImageRepository $repository
    ) {
    }

    public function create(Post $post, string $uri): PostImage
    {
        $image = $this->repository->create($post, $uri);
        $this->repository->flush();

        return $image;
    }

    public function save(PostImage $image): void
    {
        $this->repository->save($image);
        $this->repository->flush();
    }

    public function remove(PostImage $image): void
    {
        $this->repository->remove($image);
        $this->repository->flush();
    }

    public function findByIdOrNull(int $id): ?PostImage
    {
        return $this->repository->findByIdOrNull($id);
    }

    public function findById(int $id): PostImage
    {
        $image = $this->findByIdOrNull($id) ?? throw new EntityNotFoundException();

        return $image;
    }
}
