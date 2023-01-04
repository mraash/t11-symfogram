<?php

namespace App\Domain\Entity;

use App\Domain\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\Entity\User;
use Library\Exceptions\NullPropertyException;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;  /** @phpstan-ignore-line */

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $owner = null;

    /** @var Collection<int,PostImage> */
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

    public function getOwnerOrNull(): ?User
    {
        return $this->owner;
    }

    public function hasOwner(): bool
    {
        return $this->getOwnerOrNull() !== null;
    }

    public function getOwner(): User
    {
        return $this->getOwnerOrNull() ?? throw new NullPropertyException();
    }

    public function setOwner(?User $user): self
    {
        $this->owner = $user;

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
            if ($image->getPostOrNull() === $this) {
                $image->setPost(null);
            }
        }

        return $this;
    }

    public function getTitleOrNull(): ?string
    {
        return $this->title;
    }

    public function hasTitle(): bool
    {
        return $this->getTitleOrNull() !== null;
    }

    public function getTitle(): string
    {
        return $this->getTitleOrNull() ?? throw new NullPropertyException();
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
