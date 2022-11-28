<?php

namespace App\Domain\Entity;

use App\Domain\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use \App\Domain\Entity\User;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;  /** @phpstan-ignore-line */

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $owner;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: PostImage::class)]
    private Collection $images;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int,PostImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(PostImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setPost($this);
        }

        return $this;
    }

    public function removeImage(PostImage $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getPost() === $this) {
                $image->setPost(null);
            }
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
