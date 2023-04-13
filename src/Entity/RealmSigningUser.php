<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Entity;

use App\Repository\RealmSigningUserRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RealmSigningUserRepository::class)]
class RealmSigningUser
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private string $requester;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private Realm $realm;

    #[ORM\Column]
    private int $accounts;

    #[ORM\Column]
    private int $closedAccounts;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $firstIssued;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $lastValid;

    public function getRequester(): string
    {
        return $this->requester;
    }

    public function setRequester(string $requester): self
    {
        $this->requester = $requester;

        return $this;
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

    public function getAccounts(): int
    {
        return $this->accounts;
    }

    public function setAccounts(int $accounts): self
    {
        $this->accounts = $accounts;

        return $this;
    }

    public function getClosedAccounts(): int
    {
        return $this->closedAccounts;
    }

    public function getOpenAccounts(): int
    {
        return $this->getAccounts() - $this->getClosedAccounts();
    }

    public function setClosedAccounts(int $closedAccounts): self
    {
        $this->closedAccounts = $closedAccounts;

        return $this;
    }

    public function getFirstIssued(): DateTimeInterface
    {
        return $this->firstIssued;
    }

    public function setFirstIssued(DateTimeInterface $firstIssued): self
    {
        $this->firstIssued = $firstIssued;

        return $this;
    }

    public function getLastValid(): DateTimeInterface|null
    {
        return $this->lastValid;
    }

    public function setLastValid(DateTimeInterface $lastValid): self
    {
        $this->lastValid = $lastValid;

        return $this;
    }
}
