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
    private string $realm;

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
    private RealmVhost $realmVhost;

    #[ORM\OneToOne(mappedBy: 'realm', cascade: ['persist', 'remove'])]
    private RealmKey $realmKey;

    /** @var Collection<RealmNetworkProfile>  */
    #[ORM\OneToMany(mappedBy: 'realm', targetEntity: RealmNetworkProfile::class)]
    private Collection $realmNetworkProfiles;

    /** @var Collection<VhostRealm>  */
    #[ORM\OneToMany(mappedBy: 'realm', targetEntity: VhostRealm::class)]
    private Collection $vhostRealms;

    /** @var Collection<RealmHelpdesk>  */
    #[ORM\OneToMany(mappedBy: 'realm', targetEntity: RealmHelpdesk::class)]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private Collection $realmHelpdesks;

    public function __construct()
    {
        $this->realmContacts        = new ArrayCollection();
        $this->realmTrusts          = new ArrayCollection();
        $this->realmNetworkProfiles = new ArrayCollection();
        $this->vhostRealms          = new ArrayCollection();
        $this->realmHelpdesks       = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getRealm();
    }

    public function getRealm(): string
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
        $this->realmTrusts->removeElement($realmTrust);

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

    /** @return Collection<int, RealmNetworkProfile> */
    public function getRealmNetworkProfiles(): Collection
    {
        return $this->realmNetworkProfiles;
    }

    public function addRealmNetworkProfile(RealmNetworkProfile $realmNetworkProfile): self
    {
        if (!$this->realmNetworkProfiles->contains($realmNetworkProfile)) {
            $this->realmNetworkProfiles->add($realmNetworkProfile);
            $realmNetworkProfile->setRealm($this);
        }

        return $this;
    }

    public function removeRealmNetworkProfile(RealmNetworkProfile $realmNetworkProfile): self
    {
        $this->realmNetworkProfiles->removeElement($realmNetworkProfile);

        return $this;
    }

    /** @return Collection<int, VhostRealm> */
    public function getVhostRealms(): Collection
    {
        return $this->vhostRealms;
    }

    public function addVhostRealm(VhostRealm $vhostRealm): self
    {
        if (!$this->vhostRealms->contains($vhostRealm)) {
            $this->vhostRealms->add($vhostRealm);
            $vhostRealm->setRealm($this);
        }

        return $this;
    }

    public function removeVhostRealm(VhostRealm $vhostRealm): self
    {
        $this->vhostRealms->removeElement($vhostRealm);

        return $this;
    }

    /** @return Collection<int, RealmHelpdesk> */
    public function getRealmHelpdesks(): Collection
    {
        return $this->realmHelpdesks;
    }

    public function addRealmHelpdesk(RealmHelpdesk $realmHelpdesk): self
    {
        if (!$this->realmHelpdesks->contains($realmHelpdesk)) {
            $this->realmHelpdesks->add($realmHelpdesk);
            $realmHelpdesk->setRealm($this);
        }

        return $this;
    }

    public function removeRealmHelpdesk(RealmHelpdesk $realmHelpdesk): self
    {
        $this->realmHelpdesks->removeElement($realmHelpdesk);

        return $this;
    }
}
