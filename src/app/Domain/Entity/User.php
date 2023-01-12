<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Type\UserRoles;
use App\Domain\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Library\Exceptions\NullPropertyException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;  /** @phpstan-ignore-line */

    #[ORM\OneToOne(mappedBy: 'owner')]
    private EmailVerificationToken $emailVerificationToken;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?PostImage $avatar = null;

    /** @var Collection<int,Post> */
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Post::class)]
    private Collection $posts;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    /**
     * @var string[] The hierarchy -
     *  ROLE_CREATED   - has only email and password fields
     *  ROLE_VERIFIED  - email is verified
     *  ROLE_BASED     - profile is fully created (has required firstName, lastName and optional fields)
     */
    #[ORM\Column]
    private array $roles;

    /**
     * @var string The hashed password.
     */
    #[ORM\Column]
    private string $password;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 350, nullable: true)]
    private ?string $bio = null;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->roles = [UserRoles::Created->value];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmailVerificationToken(): EmailVerificationToken
    {
        return $this->emailVerificationToken;
    }

    public function setEmailVerificationToken(EmailVerificationToken $emailVerificationToken): void
    {
        // set the owning side of the relation if necessary
        if ($emailVerificationToken->getOwner() !== $this) {
            $emailVerificationToken->setOwner($this);
        }

        $this->emailVerificationToken = $emailVerificationToken;
    }

    public function getAvatarOrNull(): ?PostImage
    {
        return $this->avatar;
    }

    public function hasAvatar(): bool
    {
        return $this->getAvatarOrNull() !== null;
    }

    public function getAvatar(): PostImage
    {
        return $this->getAvatarOrNull() ?? throw new NullPropertyException();
    }

    public function setAvatar(?PostImage $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return Collection<int,Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): void
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setOwner($this);
        }
    }

    public function removePost(Post $post): void
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getOwnerOrNull() === $this) {
                $post->setOwner(null);
            }
        }
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @see UserInterface
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param UserRoles[] $roles
     */
    public function setRoles(array $roles): void
    {
        $stringRoles = array_map(fn ($roleEnum) => $roleEnum->value, $roles);

        $this->roles = $stringRoles;
    }

    public function hasRole(UserRoles $role): bool
    {
        return in_array($role->value, $this->roles);
    }

    public function addRole(UserRoles $role): void
    {
        if (!$this->hasRole($role)) {
            array_push($this->roles, $role->value);
        }
    }

    public function removeRole(UserRoles $role): void
    {
        $key = array_search($role->value, $this->roles);

        if (isset($this->roles[$key])) {
            unset($this->roles[$key]);
        }
    }

    public function setRole(UserRoles $role, bool $value): void
    {
        $value ? $this->addRole($role) : $this->removeRole($role);
    }

    public function hasVerifiedRole(): bool
    {
        return $this->hasRole(UserRoles::Verified);
    }

    public function addVerifiedRole(): void
    {
        $this->addRole(UserRoles::Verified);
    }

    public function removeVerifiedRole(): void
    {
        $this->removeRole(UserRoles::Verified);
    }

    public function setVerifiedRole(bool $value): void
    {
        $this->setRole(UserRoles::Verified, $value);
    }

    public function hasBasedRole(): bool
    {
        return $this->hasRole(UserRoles::Based);
    }

    public function addBasedRole(): void
    {
        $this->addRole(UserRoles::Based);
    }

    public function removeBasedRole(): void
    {
        $this->removeRole(UserRoles::Based);
    }

    public function setBasedRole(bool $value): void
    {
        $this->setRole(UserRoles::Based, $value);
    }

    public function getFirstName(): string|null
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string|null
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getBioOrNull(): string|null
    {
        return $this->bio;
    }

    public function setBio(?string $bio): void
    {
        $this->bio = $bio;
    }
}
