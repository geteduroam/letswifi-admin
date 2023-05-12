<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RealmHelpdeskRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RealmHelpdeskRepository::class)]
class RealmHelpdesk
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\ManyToOne(inversedBy: 'realmHelpdesks')]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private Realm $realm;

    #[ORM\Column(length: 255, nullable: true)]
    private string|null $emailAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private string|null $web = null;

    #[ORM\Column(length: 20, nullable: true)]
    private string|null $phone = null;

    #[ORM\Column(length: 4)]
    private string $lang;

    #[ORM\Column(length: 50)]
    private string $name;

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getRealm(): Realm
    {
        return $this->realm;
    }

    public function setRealm(Realm $realm): self
    {
        $this->realm = $realm;

        return $this;
    }

    public function getEmailAddress(): string|null
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string|null $emailAddress): self
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function getWeb(): string|null
    {
        return $this->web;
    }

    public function setWeb(string|null $web): self
    {
        $this->web = $web;

        return $this;
    }

    public function getPhone(): string|null
    {
        return $this->phone;
    }

    public function setPhone(string|null $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function setLang(string $lang): self
    {
        $this->lang = $lang;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
