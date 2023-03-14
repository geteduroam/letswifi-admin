<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Entity;

use App\Repository\RealmSigningLogRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

use function in_array;
use function str_replace;

#[ORM\Entity(repositoryClass: RealmSigningLogRepository::class)]
class RealmSigningLog
{
    public const USAGE_CLIENT = 'client';
    public const USAGE_SERVER = 'server';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $serial = null;

    #[ORM\Column(length: 255)]
    private string $caSub;

    #[ORM\Column(length: 255)]
    private string $requester;

    #[ORM\Column(length: 255)]
    private string $sub;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $issued;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $expires;

    /** @var resource */
    #[ORM\Column(type: Types::BLOB)]
    private $csr = null;

    /** @var resource|null */
    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $x509 = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private DateTimeInterface|null $revoked = null;

    #[ORM\Column(length: 80)]
    private string $usage;

    #[ORM\Column(length: 127, nullable: true)]
    private string|null $client = null;

    #[ORM\Column(length: 255)]
    private string $userAgent;

    #[ORM\Column(length: 39, nullable: true)]
    private string|null $ip = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private Realm $realm;

    public function getSerial(): int|null
    {
        return $this->serial;
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

    public function getCaSub(): string|null
    {
        return $this->caSub;
    }

    public function setCaSub(string $caSub): self
    {
        $this->caSub = $caSub;

        return $this;
    }

    public function getRequester(): string|null
    {
        return $this->requester;
    }

    public function setRequester(string $requester): self
    {
        $this->requester = $requester;

        return $this;
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

    public function getIssued(): DateTimeInterface|null
    {
        return $this->issued;
    }

    public function setIssued(DateTimeInterface $issued): self
    {
        $this->issued = $issued;

        return $this;
    }

    public function getExpires(): DateTimeInterface|null
    {
        return $this->expires;
    }

    public function setExpires(DateTimeInterface $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    /** @return resource */
    public function getCsr()
    {
        return $this->csr;
    }

    /** @param resource $csr */
    public function setCsr($csr): self
    {
        $this->csr = $csr;

        return $this;
    }

    /** @return resource|null */
    public function getX509()
    {
        return $this->x509;
    }

    /** @param resource $x509 */
    public function setX509($x509): self
    {
        $this->x509 = $x509;

        return $this;
    }

    public function getRevoked(): DateTimeInterface|null
    {
        return $this->revoked;
    }

    public function setRevoked(DateTimeInterface|null $revoked): self
    {
        $this->revoked = $revoked;

        return $this;
    }

    public function getUsage(): string|null
    {
        return $this->usage;
    }

    public function setUsage(string $usage): self
    {
        if (!in_array($usage, [self::USAGE_CLIENT, self::USAGE_SERVER], true)) {
            throw new InvalidArgumentException('Invalid usage');
        }

        $this->usage = $usage;

        return $this;
    }

    public function getClient(): string|null
    {
        return $this->client;
    }

    public function setClient(string $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getUserAgent(): string|null
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getIp(): string|null
    {
        return $this->ip;
    }

    public function setIp(string|null $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getSubjectWithoutCustomerName(): string|null
    {
        return str_replace('CN=', '', $this->sub);
    }

    public function setSubjectWithoutCustomerName(string $sub): self
    {
        $this->sub = $sub;

        return $this;
    }
}
