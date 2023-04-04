<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Controller;

use App\Entity\Realm;
use App\Entity\SaveRealmCommand;
use App\Repository\CARepository;
use App\Repository\NetworkProfileRepository;
use App\Repository\RealmKeyRepository;
use App\Repository\RealmNetworkProfileRepository;
use App\Repository\RealmRepository;
use App\Repository\RealmSignerRepository;
use App\Repository\RealmTrustRepository;

class SaveCommandFactory
{
    public function __construct(
        private readonly RealmRepository $realmRepository,
        private readonly CARepository $caRepository,
        private readonly RealmSignerRepository $realmSignerRepository,
        private readonly RealmTrustRepository $realmTrustRepository,
        private readonly NetworkProfileRepository $networkProfileRepository,
        private readonly RealmNetworkProfileRepository $realmNetworkProfileRepository,
        private readonly RealmKeyRepository $realmKeyRepository,
    ) {
    }

    public function buildRealmCommandByRealm(
        Realm $realm,
    ): SaveRealmCommand {
        $command = new SaveRealmCommand($realm);
        $command->setCas($this->caRepository->findAll());
        $command->setNetworkProfiles($this->networkProfileRepository->findAll());

        return $command;
    }

    public function saveRealm(SaveRealmCommand $command): void
    {
        $this->saveRealmSigner($command);
        $this->saveRealmTrusts($command);
        $this->saveRealmKey($command);
        $this->saveRealmNetworkProfile($command);
    }

    private function saveRealmSigner(SaveRealmCommand $command): void
    {
        $command->getRealmSigner()->setSignerCaSub($command->getCa());
        $command->getRealmSigner()->setDefaultValidityDays($command->getSignerDays());

        $this->realmSignerRepository->save($command->getRealmSigner(), true);
    }

    private function saveRealmTrusts(SaveRealmCommand $command): void
    {
        $this->realmTrustRepository->saveTrustsByRealm($command->getRealm(), $command->getTrustedCAs(), true);
    }

    private function saveRealmKey(SaveRealmCommand $command): void
    {
        $realm = $this->realmRepository->findOneBy(['realm' => $command->getRealm()]);

        if (!$command->getRefreshKey()) {
            return;
        }

        $key = $realm->getRealmKey()->generateKey();
        $realm->getRealmKey()->setKey($key);
        $this->realmKeyRepository->save($realm->getRealmKey(), true);
    }

    private function saveRealmNetworkProfile(SaveRealmCommand $command): void
    {
        $this->realmNetworkProfileRepository->saveByNetworkProfiles(
            $command->getRealm(),
            $command->getSelectedNetworkProfiles(),
            true,
        );
    }
}
