<?php

namespace App\Domain\Entity;

use App\Repository\Domain\Entity\PostImageRepository;
use Doctrine\ORM\Mapping as ORM;
use \App\Domain\Entity\Post;

#[ORM\Entity(repositoryClass: PostImageRepository::class)]
class PostImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;  /** @phpstn-ignore-line */

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Post $post = null;

    #[ORM\Column(length: 500)]
    private string $uri;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }
}
