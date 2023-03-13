<?php

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Realm;
use App\Entity\SaveRealmCommand;
use App\Repository\CARepository;
use App\Repository\RealmHelpdeskRepository;
use App\Repository\RealmOidRepository;
use App\Repository\RealmRepository;
use App\Repository\RealmSignerRepository;
use App\Repository\RealmSsidRepository;
use App\Repository\RealmTrustRepository;
use App\Repository\RealmVhostRepository;

class SaveCommandFactory
{
    public function __construct(
        private readonly RealmRepository $realmRepository,
        private readonly CARepository $caRepository,
        private readonly RealmSignerRepository $realmSignerRepository,
        private readonly RealmTrustRepository $realmTrustRepository,
        private readonly RealmVhostRepository $realmVhostRepository,
        private readonly RealmOidRepository $realmOidRepository,
        private readonly RealmSsidRepository $realmSsidRepository,
        private readonly RealmHelpdeskRepository $realmHelpdeskRepository,
    ) {
    }

    public function buildRealmCommandByRealm(
        Realm $realm,
    ): SaveRealmCommand {
        $command = new SaveRealmCommand($realm);
        $command->setCas($this->caRepository->findAll());

        return $command;
    }

    public function saveRealm(SaveRealmCommand $command): void
    {
        $this->saveRealmSigner($command);
        $this->saveRealmTrusts($command);
        $this->saveRealmVhost($command);
        $this->saveRealmOid($command);
        $this->saveRealmSsid($command);
        $this->saveRealmHelpdesk($command);
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

    private function saveRealmVhost(SaveRealmCommand $command): void
    {
        $realm = $this->realmRepository->findOneBy(['realm' => $command->getRealm()]);

        $realm->getRealmVhost()->setHttpHost($command->getVhost());

        $this->realmVhostRepository->save($realm->getRealmVhost(), true);
    }

    private function saveRealmOid(SaveRealmCommand $command): void
    {
        $realm = $this->realmRepository->findOneBy(['realm' => $command->getRealm()]);

        $realm->getRealmOid()->setOid($command->getOid());

        $this->realmOidRepository->save($realm->getRealmOid(), true);
    }

    private function saveRealmSsid(SaveRealmCommand $command): void
    {
        $realm = $this->realmRepository->findOneBy(['realm' => $command->getRealm()]);

        $realm->getRealmSsid()->setSsid($command->getSsid());

        $this->realmSsidRepository->save($realm->getRealmSsid(), true);
    }

    private function saveRealmHelpdesk(SaveRealmCommand $command): void
    {
        $realm = $this->realmRepository->findOneBy(['realm' => $command->getRealm()]);

        $realm->getRealmHelpdesk()->setEmailAddress($command->getEmailAddress());
        $realm->getRealmHelpdesk()->setWeb($command->getWeb());
        $realm->getRealmHelpdesk()->setPhone($command->getPhone());

        $this->realmHelpdeskRepository->save($realm->getRealmHelpdesk(), true);
    }
}
