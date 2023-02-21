<?php

namespace App\Entity;

use App\Repository\RealmSigningLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RealmSigningLogRepository::class)]
class RealmSigningLog
{
    const USAGE_CLIENT = 'client';
    const USAGE_SERVER = 'server';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $serial = null;

    #[ORM\Column(length: 127)]
    private ?string $realm = null;

    #[ORM\Column(length: 255)]
    private ?string $ca_sub = null;

    #[ORM\Column(length: 255)]
    private ?string $requester = null;

    #[ORM\Column(length: 255)]
    private ?string $sub = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $issued = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $expires = null;

    #[ORM\Column(type: Types::BLOB)]
    private $csr = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $x509 = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $revoked = null;

    #[ORM\Column(length: 80)]
    private ?string $usage = null;

    #[ORM\Column(length: 127, nullable: true)]
    private ?string $client = null;

    #[ORM\Column(length: 255)]
    private ?string $user_agent = null;

    #[ORM\Column(length: 39, nullable: true)]
    private ?string $ip = null;

    public function getSerial(): ?int
    {
        return $this->serial;
    }

    public function getRealm(): ?string
    {
        return $this->realm;
    }

    public function setRealm(string $realm): self
    {
        $this->realm = $realm;

        return $this;
    }

    public function getCaSub(): ?string
    {
        return $this->ca_sub;
    }

    public function setCaSub(string $ca_sub): self
    {
        $this->ca_sub = $ca_sub;

        return $this;
    }

    public function getRequester(): ?string
    {
        return $this->requester;
    }

    public function setRequester(string $requester): self
    {
        $this->requester = $requester;

        return $this;
    }

    public function getSub(): ?string
    {
        return $this->sub;
    }

    public function setSub(string $sub): self
    {
        $this->sub = $sub;

        return $this;
    }

    public function getIssued(): ?\DateTimeInterface
    {
        return $this->issued;
    }

    public function setIssued(\DateTimeInterface $issued): self
    {
        $this->issued = $issued;

        return $this;
    }

    public function getExpires(): ?\DateTimeInterface
    {
        return $this->expires;
    }

    public function setExpires(\DateTimeInterface $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    public function getCsr()
    {
        return $this->csr;
    }

    public function setCsr($csr): self
    {
        $this->csr = $csr;

        return $this;
    }

    public function getX509()
    {
        return $this->x509;
    }

    public function setX509($x509): self
    {
        $this->x509 = $x509;

        return $this;
    }

    public function getRevoked(): ?\DateTimeInterface
    {
        return $this->revoked;
    }

    public function setRevoked(?\DateTimeInterface $revoked): self
    {
        $this->revoked = $revoked;

        return $this;
    }

    public function getUsage(): ?string
    {
        return $this->usage;
    }

    public function setUsage(string $usage): self
    {
        if (!in_array($usage, array(self::USAGE_CLIENT, self::USAGE_SERVER))) {
            throw new \InvalidArgumentException("Invalid usage");
        }

        $this->usage = $usage;

        return $this;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(string $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->user_agent;
    }

    public function setUserAgent(string $user_agent): self
    {
        $this->user_agent = $user_agent;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getSubjectWithoutCustomerName(): ?string
    {
        return str_replace('CN=', '', $this->sub);
    }

    public function setSubjectWithoutCustomerName(string $sub): self
    {
        $this->sub = $sub;

        return $this;
    }
}
