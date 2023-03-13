<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Entity;

use App\Repository\RealmOidRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RealmOidRepository::class)]
class RealmOid
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'realmOid', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private Realm|null $realm = null;

    #[ORM\Column(length: 255)]
    private string|null $oid = null;

    public function getRealm(): Realm|null
    {
        return $this->realm;
    }

    public function setRealm(Realm $realm): self
    {
        $this->realm = $realm;

        return $this;
    }

    public function getOid(): string|null
    {
        return $this->oid;
    }

    public function setOid(string $oid): self
    {
        $this->oid = $oid;

        return $this;
    }
}
