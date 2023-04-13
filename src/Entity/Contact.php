<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use function count;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 150)]
    private string $nameId;

    #[ORM\Column(length: 255)]
    private string $displayName;

    #[ORM\Column(length: 255)]
    private string $userId;

    #[ORM\Column(length: 255)]
    private string $password;

    #[ORM\Column]
    private bool $superAdmin;

    /** @var array<string> */
    private array $roles = [];

    /** @var Collection<RealmContact> */
    #[ORM\OneToMany(mappedBy: 'contact', targetEntity: RealmContact::class, cascade: ['remove'])]
    private Collection $realmContacts;

    public function __construct()
    {
        $this->realmContacts = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getUserId();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNameId(): string
    {
        return $this->nameId;
    }

    public function setNameId(string $nameId): self
    {
        $this->nameId = $nameId;

        return $this;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getSuperAdmin(): bool
    {
        return $this->superAdmin;
    }

    public function setSuperAdmin(bool $superAdmin): self
    {
        $this->superAdmin = $superAdmin;

        return $this;
    }

    public function assignRole(string $role): void
    {
        $this->roles[] = $role;
    }

    /** @return array|string[] */
    public function getRoles(): array
    {
        if (count($this->roles) === 0) {
            $this->roles[] = $this->getSuperAdmin() ? 'ROLE_SUPER_ADMIN' : $this->roles[] = 'ROLE_ADMIN';
        }

        return $this->roles;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->getUserId();
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isOwnerOfRealm(Realm $realm): bool
    {
        $realmContacts = $this->getRealmContacts();
        foreach ($realmContacts as $realmContact) {
            if ($realm->getRealm() === $realmContact->getRealm()->getRealm()) {
                return true;
            }
        }

        return false;
    }

    /** @return Collection<int, RealmContact> */
    public function getRealmContacts(): Collection
    {
        return $this->realmContacts;
    }
}
