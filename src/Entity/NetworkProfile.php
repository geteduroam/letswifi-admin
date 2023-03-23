<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Entity;

use App\Repository\NetworkProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NetworkProfileRepository::class)]
class NetworkProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(length: 20)]
    private string|null $typeName = null;

    #[ORM\Column(length: 255)]
    private string|null $name = null;

    #[ORM\Column(length: 255)]
    private string|null $value = null;

    /** @var Collection<RealmNetworkProfile>  */
    #[ORM\OneToMany(mappedBy: 'NetworkProfile', targetEntity: RealmNetworkProfile::class)]
    private Collection $realmNetworkProfiles;

    public function __construct()
    {
        $this->realmNetworkProfiles = new ArrayCollection();
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getTypeName(): string|null
    {
        return $this->typeName;
    }

    public function setTypeName(string $typeName): self
    {
        $this->typeName = $typeName;

        return $this;
    }

    public function getName(): string|null
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): string|null
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

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
            $realmNetworkProfile->setNetworkProfile($this);
        }

        return $this;
    }

    public function removeRealmNetworkProfile(RealmNetworkProfile $realmNetworkProfile): self
    {
        if ($this->realmNetworkProfiles->removeElement($realmNetworkProfile)) {
            // set the owning side to null (unless already changed)
            if ($realmNetworkProfile->getNetworkProfile() === $this) {
                $realmNetworkProfile->setNetworkProfile(null);
            }
        }

        return $this;
    }
}
