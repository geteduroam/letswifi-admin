<?php

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

declare(strict_types=1);

namespace App\Application\Factory;

use App\Application\Command\SaveRealmCommand;
use App\Entity\Realm;
use App\Repository\CARepository;
use App\Repository\NetworkProfileRepository;

class SaveRealmCommandFactory
{
    public function __construct(
        private readonly CARepository $caRepository,
        private readonly NetworkProfileRepository $networkProfileRepository,
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
}
