<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Application\Command;

use App\Entity\CA;
use App\Entity\NetworkProfile;
use App\Entity\Realm;
use App\Entity\RealmNetworkProfile;
use App\Entity\RealmTrust;

use function count;

class SaveRealmCommand
{
    private string $realm;

    private int $signerDays;

    /** @var array<RealmNetworkProfile>  */
    private array $realmNetworkProfiles;

    /** @var array<int, NetworkProfile>  */
    private array $networkProfiles = [];

    /** @var array<int, NetworkProfile>  */
    private array $selectedNetworkProfiles;

    private CA $ca;

    /** @var array<RealmTrust>  */
    private array $trusts;

    private string $key;
    /** @var array<string> */
    private array $cas;

    /** @var array<string>  */
    private array $trustedCas = [];

    private bool $refreshKey;

    public function __construct(
        Realm $realm,
    ) {
        $this->setRealm($realm->getRealm())
        ->setRealmNetworkProfiles($realm->getRealmNetworkProfiles()->toArray())
        ->setNetworkProfilesByRealmNetworkProfiles($realm->getRealmNetworkProfiles()->toArray())
        ->setTrusts($realm->getRealmTrusts()->toArray())
        ->setRefreshKey(false);

        if ($realm->getRealmSigner() === null) {
            return;
        }

        $this->setSignerDays($realm->getRealmSigner()->getDefaultValidityDays());
        $this->setCa($realm->getRealmSigner()->getSignerCaSub());
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

    public function getSignerDays(): int
    {
        return $this->signerDays;
    }

    public function setSignerDays(int $signerDays): self
    {
        $this->signerDays = $signerDays;

        return $this;
    }

    /** @return array<int, NetworkProfile> */
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

    /** @return array<int, NetworkProfile> */
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
            if ($realmNetworkProfile->getNetworkProfile() === null) {
                continue;
            }

            $this->selectedNetworkProfiles[$realmNetworkProfile->getNetworkProfile()->getId()] =
                $realmNetworkProfile->getNetworkProfile();
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
        if (count($this->trustedCas) === 0) {
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

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
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
