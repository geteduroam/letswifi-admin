<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Entity;

use App\Repository\RealmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RealmRepository::class)]
class Realm
{
    #[ORM\Id]
    #[ORM\Column(length: 127)]
    private string|null $realm = null;

    /** @var Collection<RealmContact> */
    #[ORM\OneToMany(mappedBy: 'contact', targetEntity: RealmContact::class)]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private Collection $realmContacts;

    public function __construct()
    {
        $this->realmContacts = new ArrayCollection();
    }

    public function getRealm(): string|null
    {
        return $this->realm;
    }

    public function setRealm(string $realm): self
    {
        $this->realm = $realm;

        return $this;
    }

    /** @return Collection<int, RealmContact> */
    public function getRealmContacts(): Collection
    {
        return $this->realmContacts;
    }
}
