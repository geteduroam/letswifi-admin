<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Entity;

use App\Repository\RealmNetworkProfileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RealmNetworkProfileRepository::class)]
class RealmNetworkProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\ManyToOne(inversedBy: 'realmNetworkProfiles')]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private Realm $realm;

    #[ORM\ManyToOne(inversedBy: 'realmNetworkProfiles')]
    private NetworkProfile $networkProfile;

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

    public function getNetworkProfile(): NetworkProfile
    {
        return $this->networkProfile;
    }

    public function setNetworkProfile(NetworkProfile $networkProfile): self
    {
        $this->networkProfile = $networkProfile;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getId();
    }
}
