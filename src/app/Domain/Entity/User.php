<?php

namespace App\Domain\Entity;

use App\Domain\Constant\UserRoles;
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
     * @var UserRoles[] The hierarchy -
     *  CREATED   - has only email and password fields
     *  VERIFIED  - email is verified
     *  BASED     - has firstName, lastName and bio fields
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

    public function setEmailVerificationToken(EmailVerificationToken $emailVerificationToken): self
    {
        // set the owning side of the relation if necessary
        if ($emailVerificationToken->getOwner() !== $this) {
            $emailVerificationToken->setOwner($this);
        }

        $this->emailVerificationToken = $emailVerificationToken;

        return $this;
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

    public function setAvatar(?PostImage $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int,Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setOwner($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getOwnerOrNull() === $this) {
                $post->setOwner(null);
            }
        }

        return $this;
    }

    public function getEmail(): string
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
        $roles[] = UserRoles::Created->value;

        return array_unique($roles);
    }

    public function hasRole(UserRoles $role): bool
    {
        return in_array($role->value, $this->roles);
    }

    /**
     * @param UserRoles[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(UserRoles $role): self
    {
        if (!$this->hasRole($role)) {
            array_push($this->roles, $role->value);
        }

        return $this;
    }

    public function removeRole(UserRoles $role): self
    {
        $key = array_search($role->value, $this->roles);

        if (isset($this->roles[$key])) {
            unset($this->roles[$key]);
        }

        return $this;
    }

    public function setRole(UserRoles $role, bool $value): self
    {
        $value ? $this->addRole($role) : $this->removeRole($role);

        return $this;
    }

    public function hasVerifiedRole(): bool
    {
        return $this->hasRole(UserRoles::Verified);
    }

    public function addVerifiedRole(): self
    {
        $this->addRole(UserRoles::Verified);

        return $this;
    }

    public function removeVerifiedRole(): self
    {
        $this->removeRole(UserRoles::Verified);

        return $this;
    }

    public function setVerifiedRole(bool $value): self
    {
        $this->setRole(UserRoles::Verified, $value);

        return $this;
    }

    public function hasBasedRole(): bool
    {
        return $this->hasRole(UserRoles::Based);
    }

    public function addBasedRole(): self
    {
        $this->addRole(UserRoles::Based);

        return $this;
    }

    public function removeBasedRole(): self
    {
        $this->removeRole(UserRoles::Based);

        return $this;
    }

    public function setBasedRole(bool $value): self
    {
        $this->setRole(UserRoles::Based, $value);

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

    public function getFirstName(): string|null
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string|null
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBioOrNull(): string|null
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }
}
