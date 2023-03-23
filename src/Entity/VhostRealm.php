<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Entity;

use App\Repository\VhostRealmRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VhostRealmRepository::class)]
class VhostRealm
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(length: 255)]
    private string|null $httpHost = null;

    #[ORM\Column(length: 255)]
    private string|null $pathPrefix = null;

    #[ORM\Column(length: 80)]
    private string|null $authService = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private string|null $authConfig = null;

    #[ORM\ManyToOne(inversedBy: 'vhostRealms')]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private Realm|null $realm = null;

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getHttpHost(): string|null
    {
        return $this->httpHost;
    }

    public function setHttpHost(string $httpHost): self
    {
        $this->httpHost = $httpHost;

        return $this;
    }

    public function getPathPrefix(): string|null
    {
        return $this->pathPrefix;
    }

    public function setPathPrefix(string $pathPrefix): self
    {
        $this->pathPrefix = $pathPrefix;

        return $this;
    }

    public function getAuthService(): string|null
    {
        return $this->authService;
    }

    public function setAuthService(string $authService): self
    {
        $this->authService = $authService;

        return $this;
    }

    public function getAuthConfig(): string|null
    {
        return $this->authConfig;
    }

    public function setAuthConfig(string|null $authConfig): self
    {
        $this->authConfig = $authConfig;

        return $this;
    }

    public function getRealm(): Realm|null
    {
        return $this->realm;
    }

    public function setRealm(Realm|null $realm): self
    {
        $this->realm = $realm;

        return $this;
    }
}
