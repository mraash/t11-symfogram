<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Repository\PostImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Library\Exceptions\NullPropertyException;

#[ORM\Entity(repositoryClass: PostImageRepository::class)]
class PostImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;  /** @phpstan-ignore-line */

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Post $post = null;

    #[ORM\Column(length: 500)]
    private string $uri;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPostOrNull(): ?Post
    {
        return $this->post;
    }

    public function hasPost(): bool
    {
        return $this->getPostOrNull() !== null;
    }

    public function getPost(): Post
    {
        return $this->getPostOrNull() ?? throw new NullPropertyException();
    }

    public function setPost(?Post $post): void
    {
        $this->post = $post;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }
}
