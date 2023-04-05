<?php

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

declare(strict_types=1);

namespace App\Application\CommandHandler;

use App\Application\Command\SaveRealmCommand;
use App\Repository\RealmKeyRepository;
use App\Repository\RealmNetworkProfileRepository;
use App\Repository\RealmRepository;
use App\Repository\RealmSignerRepository;
use App\Repository\RealmTrustRepository;

class SaveRealmCommandHandler
{
    public function __construct(
        private readonly RealmRepository $realmRepository,
        private readonly RealmSignerRepository $realmSignerRepository,
        private readonly RealmTrustRepository $realmTrustRepository,
        private readonly RealmNetworkProfileRepository $realmNetworkProfileRepository,
        private readonly RealmKeyRepository $realmKeyRepository,
    ) {
    }

    public function save(SaveRealmCommand $command): void
    {
        $this->saveRealmSigner($command);
        $this->saveRealmTrusts($command);
        $this->saveRealmKey($command);
        $this->saveRealmNetworkProfile($command);
    }

    private function saveRealmSigner(SaveRealmCommand $command): void
    {
        $realmSigner = $this->realmSignerRepository->find($command->getRealm());

        $realmSigner->setSignerCaSub($command->getCa());
        $realmSigner->setDefaultValidityDays($command->getSignerDays());

        $this->realmSignerRepository->save($realmSigner, true);
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

        $realm->getRealmKey()->generateKey();
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
