<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Entity;

use App\Repository\RealmTrustRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RealmTrustRepository::class)]
class RealmTrust
{
    #[ORM\Id]
    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'realmTrusts')]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private Realm|null $realm;

    #[ORM\Id]
    #[ORM\ManyToOne(cascade: ['persist'], fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'trusted_ca_sub', referencedColumnName: 'sub', nullable: false)]
    private CA $trustedCaSub;

    public function getRealm(): Realm|null
    {
        return $this->realm;
    }

    public function setRealm(Realm|null $realm): self
    {
        $this->realm = $realm;

        return $this;
    }

    public function getTrustedCaSub(): CA
    {
        return $this->trustedCaSub;
    }

    public function setTrustedCaSub(CA $trustedCaSub): self
    {
        $this->trustedCaSub = $trustedCaSub;

        return $this;
    }

    public function __toString(): string
    {
        return $this->trustedCaSub->getSub();
    }
}
