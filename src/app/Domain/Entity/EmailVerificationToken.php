<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Repository\EmailVerificationTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailVerificationTokenRepository::class)]
class EmailVerificationToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;  /** @phpstan-ignore-line */

    #[ORM\OneToOne(inversedBy: 'emailVerificationToken')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $owner;

    #[ORM\Column(length: 255)]
    private string $token;

    public function getId(): int
    {
        return $this->id;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $user): void
    {
        $this->owner = $user;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }
}
