<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Controller\Admin\Helper;

use App\Entity\RealmSigningUser;
use App\Repository\RealmSigningLogRepository;

class RealmSigningUserHelper
{
    public function __construct(
        private readonly RealmSigningLogRepository $realmSigningLogRepository,
    ) {
    }

    public function revoke(RealmSigningUser $realmSigningUser): void
    {
        $realmSigningLogs = $this->realmSigningLogRepository->findByRequesterAndRealm(
            $realmSigningUser->getRequester(),
            $realmSigningUser->getRealm()->getRealm(),
        );

        foreach ($realmSigningLogs as $realmSigningLog) {
            $this->realmSigningLogRepository->revoke($realmSigningLog, true);
        }
    }
}
