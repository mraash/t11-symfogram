<?php

namespace App\Domain\Entity;

use App\Domain\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(mappedBy: 'owner')]
    private ?EmailVerificationToken $emailVerificationToken = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var string[]
     */
    #[ORM\Column]
    private array $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password.
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var bool Is email verified.
     */
    #[ORM\Column]
    private ?bool $isVerified = false;

    /**
     * @var bool Is profile information given (full name, bio, avatar) after email verification.
     */
    #[ORM\Column]
    private ?bool $isBased = false;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 350, nullable: true)]
    private ?string $bio = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsEmailVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getEmailVerificationToken(): ?EmailVerificationToken
    {
        return $this->emailVerificationToken;
    }

    public function setEmailVerificationToken(EmailVerificationToken $emailVerificationToken): self
    {
        // set the owning side of the relation if necessary
        if ($emailVerificationToken->getOwner() !== $this) {
            $emailVerificationToken->setOwner($this);
        }

        $this->emailVerificationToken = $emailVerificationToken;

        return $this;
    }

    public function isBased(): ?bool
    {
        return $this->isBased;
    }

    public function setIsBased(bool $isBased): self
    {
        $this->isBased = $isBased;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }
}
