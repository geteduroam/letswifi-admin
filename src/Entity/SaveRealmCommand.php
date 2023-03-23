<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Entity;

class SaveRealmCommand
{
    private string $realm;

    private int $signerDays;

    private RealmSigner $realmSigner;

    /** @var array<string>  */
    private array $realmNetworkProfiles;

    /** @var array<string>  */
    private array $networkProfiles;

    /** @var array<string>  */
    private array $selectedNetworkProfiles;

    private CA $ca;

    /** @var array<RealmTrust>  */
    private array $trusts;

    private string $key;

    private string $vHost;

    /** @var array<string> */
    private array $cas;

    /** @var array<string>  */
    private array $trustedCas = [];

    private bool $refreshKey;

    public function __construct(
        Realm $realm,
    ) {
        $this->setRealm($realm->getRealm())
        ->setRealmSigner($realm->getRealmSigner())
//        ->setRealmNetworkProfiles($realm->getRealmNetworkProfiles()->toArray())
        ->setNetworkProfilesByRealmNetworkProfiles($realm->getRealmNetworkProfiles()->toArray())
        ->setSignerDays($realm->getRealmSigner()->getDefaultValidityDays())
        ->setCa($realm->getRealmSigner()?->getSignerCaSub())
        ->setTrusts($realm->getRealmTrusts()->toArray())
        ->setRefreshKey(false);
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

    public function getSignerDays(): int
    {
        return $this->signerDays;
    }

    public function setSignerDays(int $signerDays): self
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

    /** @return array<string> */
    public function getNetworkProfiles(): array
    {
        return $this->networkProfiles;
    }

    /** @param array<NetworkProfile> $networkProfiles */
    public function setNetworkProfiles(array $networkProfiles): self
    {
        foreach ($networkProfiles as $networkProfile) {
            $this->networkProfiles[$networkProfile->getId()] = $networkProfile;
        }

        return $this;
    }

    /** @return array<NetworkProfile> */
    public function getSelectedNetworkProfiles(): array
    {
        return $this->selectedNetworkProfiles;
    }

    /** @param array<RealmNetworkProfile> $networkProfiles */
    public function setSelectedNetworkProfiles(array $networkProfiles): self
    {
        unset($this->selectedNetworkProfiles);
        foreach ($networkProfiles as $networkProfile) {
            $this->selectedNetworkProfiles[$networkProfile->getId()] = $networkProfile;
        }

        return $this;
    }

    /** @param array<RealmNetworkProfile> $realmNetworkProfiles */
    public function setNetworkProfilesByRealmNetworkProfiles(array $realmNetworkProfiles): self
    {
        unset($this->selectedNetworkProfiles);
        foreach ($realmNetworkProfiles as $realmNetworkProfile) {
            $this->selectedNetworkProfiles[$realmNetworkProfile->getNetworkProfile()->getId()] = $realmNetworkProfile->getNetworkProfile();
        }

        return $this;
    }

    /** @return array<RealmNetworkProfile> */
    public function getRealmNetworkProfiles(): array
    {
        return $this->realmNetworkProfiles;
    }

    /** @param array<RealmNetworkProfile> $realmNetworkProfiles */
    public function setRealmNetworkProfiles(array $realmNetworkProfiles): self
    {
        $this->realmNetworkProfiles = $realmNetworkProfiles;

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

    public function getRefreshKey(): bool
    {
        return $this->refreshKey;
    }

    public function setRefreshKey(bool $refreshKey): self
    {
        $this->refreshKey = $refreshKey;

        return $this;
    }
}
