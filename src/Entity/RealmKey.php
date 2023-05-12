<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Entity;

use App\Repository\RealmKeyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use function base64_encode;
use function random_bytes;
use function stream_get_contents;

#[ORM\Entity(repositoryClass: RealmKeyRepository::class)]
class RealmKey
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'realmKey', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private Realm $realm;

    /** @var resource */
    #[ORM\Column(name: '`key`', type: Types::BLOB)]
    private $key = null;

    #[ORM\Column]
    private int $issued;

    #[ORM\Column]
    private int $expires;

    public function getRealm(): Realm
    {
        return $this->realm;
    }

    public function setRealm(Realm $realm): self
    {
        $this->realm = $realm;

        return $this;
    }

    public function getKey(): string|null
    {
        $result = stream_get_contents($this->key);
        if ($result === false) {
            return null;
        }

        return $result;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getIssued(): int
    {
        return $this->issued;
    }

    public function setIssued(int $issued): self
    {
        $this->issued = $issued;

        return $this;
    }

    public function getExpires(): int
    {
        return $this->expires;
    }

    public function setExpires(int $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    public function generateKey(): void
    {
        $this->setKey(base64_encode(random_bytes(32)));
    }
}
