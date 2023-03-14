<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Entity;

use App\Repository\RealmVhostRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RealmVhostRepository::class)]
class RealmVhost
{
    #[ORM\Column(length: 127)]
    private string $httpHost;

    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'realmVhost', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private Realm $realm;

    public function getHttpHost(): string|null
    {
        return $this->httpHost;
    }

    public function setHttpHost(string $httpHost): self
    {
        $this->httpHost = $httpHost;

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
}
