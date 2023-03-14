<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Entity;

use DateTime;

class SaveRealmCommand
{
    private string $realm;

    private DateTime $signerDays;

    private RealmSigner $realmSigner;

    private CA $ca;

    /** @var array<RealmTrust>  */
    private array $trusts;

    private string $key;

    private string $vHost;

    private string $oid;

    private string $ssid;

    /** @var array<string> */
    private array $cas;

    /** @var array<string>  */
    private array $trustedCas = [];

    private string $emailAddress;

    private string $web;

    private string $phone;

    public function __construct(
        Realm $realm,
    ) {
        $this->setRealm($realm->getRealm())
        ->setRealmSigner($realm->getRealmSigner())
        ->setSignerDays($realm->getRealmSigner()->getDefaultValidityDays())
        ->setCa($realm->getRealmSigner()?->getSignerCaSub())
        ->setTrusts($realm->getRealmTrusts()->toArray())
        ->setKey($realm->getRealmKey()->getKeyAsString())
        ->setVhost($realm->getRealmVhost()->getHttpHost())
        ->setSsid($realm->getRealmSsid()->getSsid())
        ->setOid($realm->getRealmOid()->getOid())
        ->setEmailAddress($realm->getRealmHelpdesk()->getEmailAddress())
        ->setWeb($realm->getRealmHelpdesk()->getWeb())
        ->setPhone($realm->getRealmHelpdesk()->getPhone());
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

    public function getSignerDays(): DateTime
    {
        return $this->signerDays;
    }

    public function setSignerDays(DateTime $signerDays): self
    {
        $this->signerDays = $signerDays;

        return $this;
    }

    public function getRealmSigner(): RealmSigner
    {
        return $this->realmSigner;
    }

    public function setRealmSigner(RealmSigner $realmSigner): self
    {
        $this->realmSigner = $realmSigner;

        return $this;
    }

    public function getCa(): CA
    {
        return $this->ca;
    }

    /** @return $this */
    public function setCa(CA $ca): self
    {
        $this->ca = $ca;

        return $this;
    }

    /** @return array<string> */
    public function getCas(): array
    {
        return $this->cas;
    }

    /** @param array<CA> $cas */
    public function setCas(array $cas): self
    {
        foreach ($cas as $ca) {
            $this->cas[$ca->getSub()] = $ca->getSub();
        }

        return $this;
    }

    /** @param array<string> $trustedCas */
    public function setTrustedCas(array $trustedCas): self
    {
        unset($this->trustedCas);
        foreach ($trustedCas as $trustedCa) {
            $this->trustedCas[$trustedCa] = $trustedCa;
        }

        return $this;
    }

    /** @return array<string> */
    public function getTrustedCAs(): array
    {
        if (empty($this->trustedCas)) {
            foreach ($this->trusts as $trust) {
                $this->trustedCas[$trust->getTrustedCaSub()->getSub()] = $trust->getTrustedCaSub()->getSub();
            }
        }

        return $this->trustedCas;
    }

    /** @param array<RealmTrust> $trusts */
    public function setTrusts(array $trusts): self
    {
        $this->trusts = $trusts;

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

    public function getVhost(): string|null
    {
        return $this->vHost;
    }

    public function setVhost(string|null $vHost): self
    {
        $this->vHost = $vHost;

        return $this;
    }

    public function getOid(): string|null
    {
        return $this->oid;
    }

    public function setOid(string|null $oid): self
    {
        $this->oid = $oid;

        return $this;
    }

    public function getSsid(): string|null
    {
        return $this->ssid;
    }

    public function setSsid(string|null $ssid): self
    {
        $this->ssid = $ssid;

        return $this;
    }

    public function getEmailAddress(): string|null
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string|null $emailAddress): self
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function getWeb(): string|null
    {
        return $this->web;
    }

    public function setWeb(string|null $web): self
    {
        $this->web = $web;

        return $this;
    }

    public function getPhone(): string|null
    {
        return $this->phone;
    }

    public function setPhone(string|null $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
}
