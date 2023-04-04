<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Entity;

use App\Repository\CARepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CARepository::class)]
class CA
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private string|null $sub = null;

    #[ORM\Column(type: Types::TEXT)]
    private string|null $pub = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private string|null $key = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'cAs')]
    #[ORM\JoinColumn(name: 'issuer', referencedColumnName: 'sub')]
    private self|null $issuer = null;

    /** @var Collection<CA>  */
    #[ORM\OneToMany(mappedBy: 'issuer', targetEntity: self::class)]
    private Collection $cAs;

    public function __construct()
    {
        $this->cAs = new ArrayCollection();
    }

    public function getSub(): string|null
    {
        return $this->sub;
    }

    public function setSub(string $sub): self
    {
        $this->sub = $sub;

        return $this;
    }

    public function getPub(): string|null
    {
        return $this->pub;
    }

    public function setPub(string $pub): self
    {
        $this->pub = $pub;

        return $this;
    }

    public function getKey(): string|null
    {
        return $this->key;
    }

    public function setKey(string|null $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getIssuer(): self|null
    {
        return $this->issuer;
    }

    public function setIssuer(self|null $issuer): self
    {
        $this->issuer = $issuer;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getSub();
    }

    /** @return Collection<int, self> */
    public function getCAs(): Collection
    {
        return $this->cAs;
    }

    public function addCA(self $cA): self
    {
        if (!$this->cAs->contains($cA)) {
            $this->cAs->add($cA);
            $cA->setIssuer($this);
        }

        return $this;
    }

    public function removeCA(self $cA): self
    {
        if ($this->cAs->removeElement($cA)) {
            // set the owning side to null (unless already changed)
            if ($cA->getIssuer() === $this) {
                $cA->setIssuer(null);
            }
        }

        return $this;
    }
}
