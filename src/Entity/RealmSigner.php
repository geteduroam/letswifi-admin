<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Entity;

use App\Repository\RealmSignerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RealmSignerRepository::class)]
class RealmSigner
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'realmSigner', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private Realm|null $realm = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'signer_ca_sub', referencedColumnName: 'sub', nullable: false)]
    private CA|null $signerCaSub = null;

    #[ORM\Column]
    private int|null $defaultValidityDays = null;

    public function getRealm(): Realm|null
    {
        return $this->realm;
    }

    public function setRealm(Realm $realm): self
    {
        $this->realm = $realm;

        return $this;
    }

    public function getSignerCaSub(): CA|null
    {
        return $this->signerCaSub;
    }

    public function setSignerCaSub(CA|null $signerCaSub): self
    {
        $this->signerCaSub = $signerCaSub;

        return $this;
    }

    public function getDefaultValidityDays(): int
    {
        return $this->defaultValidityDays;
    }

    public function setDefaultValidityDays(int $defaultValidityDays): self
    {
        $this->defaultValidityDays = $defaultValidityDays;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getSignerCaSub()->getSub();
    }
}
