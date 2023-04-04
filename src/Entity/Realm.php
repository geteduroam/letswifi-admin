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
    #[ORM\OneToMany(mappedBy: 'realm', targetEntity: RealmContact::class)]
    private Collection $realmContacts;

    #[ORM\OneToOne(mappedBy: 'realm', cascade: ['persist', 'remove'])]
    private RealmSigner|null $realmSigner = null;

    /** @var Collection<RealmTrust> */
    #[ORM\OneToMany(mappedBy: 'realm', targetEntity: RealmTrust::class)]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private Collection $realmTrusts;

    #[ORM\OneToOne(mappedBy: 'realm', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private RealmVhost $realmVhost;

    #[ORM\OneToOne(mappedBy: 'realm', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private RealmKey|null $realmKey = null;

    #[ORM\OneToOne(mappedBy: 'realm', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private RealmSsid|null $realmSsid = null;

    #[ORM\OneToOne(mappedBy: 'realm', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private RealmOid|null $realmOid = null;

    #[ORM\OneToOne(mappedBy: 'realm', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private RealmHelpdesk|null $realmHelpdesk = null;

    public function __construct()
    {
        $this->realmContacts = new ArrayCollection();
        $this->realmTrusts   = new ArrayCollection();
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

    public function getRealmSigner(): RealmSigner|null
    {
        return $this->realmSigner;
    }

    public function setRealmSigner(RealmSigner $realmSigner): self
    {
        // set the owning side of the relation if necessary
        if ($realmSigner->getRealm() !== $this) {
            $realmSigner->setRealm($this);
        }

        $this->realmSigner = $realmSigner;

        return $this;
    }

    /** @return Collection<int, RealmTrust> */
    public function getRealmTrusts(): Collection
    {
        return $this->realmTrusts;
    }

    public function addRealmTrust(RealmTrust $realmTrust): self
    {
        if (!$this->realmTrusts->contains($realmTrust)) {
            $this->realmTrusts->add($realmTrust);
            $realmTrust->setRealm($this);
        }

        return $this;
    }

    public function removeRealmTrust(RealmTrust $realmTrust): self
    {
        if ($this->realmTrusts->removeElement($realmTrust)) {
            // set the owning side to null (unless already changed)
            if ($realmTrust->getRealm() === $this) {
                $realmTrust->setRealm(null);
            }
        }

        return $this;
    }

    public function getRealmVhost(): RealmVhost|null
    {
        return $this->realmVhost;
    }

    public function setRealmVhost(RealmVhost $realmVhost): self
    {
        // set the owning side of the relation if necessary
        if ($realmVhost->getRealm() !== $this) {
            $realmVhost->setRealm($this);
        }

        $this->realmVhost = $realmVhost;

        return $this;
    }

    public function getRealmKey(): RealmKey|null
    {
        return $this->realmKey;
    }

    public function setRealmKey(RealmKey $realmKey): self
    {
        // set the owning side of the relation if necessary
        if ($realmKey->getRealm() !== $this) {
            $realmKey->setRealm($this);
        }

        $this->realmKey = $realmKey;

        return $this;
    }

    public function getRealmSsid(): RealmSsid|null
    {
        return $this->realmSsid;
    }

    public function setRealmSsid(RealmSsid $realmSsid): self
    {
        // set the owning side of the relation if necessary
        if ($realmSsid->getRealm() !== $this) {
            $realmSsid->setRealm($this);
        }

        $this->realmSsid = $realmSsid;

        return $this;
    }

    public function getRealmOid(): RealmOid|null
    {
        return $this->realmOid;
    }

    public function setRealmOid(RealmOid $realmOid): self
    {
        // set the owning side of the relation if necessary
        if ($realmOid->getRealm() !== $this) {
            $realmOid->setRealm($this);
        }

        $this->realmOid = $realmOid;

        return $this;
    }

    public function getRealmHelpdesk(): RealmHelpdesk|null
    {
        return $this->realmHelpdesk;
    }

    public function setRealmHelpdesk(RealmHelpdesk|null $realmHelpdesk): self
    {
        // unset the owning side of the relation if necessary
        if ($realmHelpdesk === null && $this->realmHelpdesk !== null) {
            $this->realmHelpdesk->setRealm(null);
        }

        // set the owning side of the relation if necessary
        if ($realmHelpdesk !== null && $realmHelpdesk->getRealm() !== $this) {
            $realmHelpdesk->setRealm($this);
        }

        $this->realmHelpdesk = $realmHelpdesk;

        return $this;
    }
}
