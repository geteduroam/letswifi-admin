<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Entity;

use App\Repository\RealmHelpdeskRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RealmHelpdeskRepository::class)]
class RealmHelpdesk
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'realmHelpdesk', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private Realm|null $realm = null;

    #[ORM\Column(length: 255)]
    private string|null $emailAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private string|null $web = null;

    #[ORM\Column(length: 20)]
    private string|null $phone = null;

    public function getRealm(): Realm|null
    {
        return $this->realm;
    }

    public function setRealm(Realm|null $realm): self
    {
        $this->realm = $realm;

        return $this;
    }

    public function getEmailAddress(): string|null
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): self
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

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
}
