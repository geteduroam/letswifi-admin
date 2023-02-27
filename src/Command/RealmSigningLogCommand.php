<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Command;

use App\Entity\RealmSigningLog;
use App\Repository\RealmSigningLogRepository;

use function end;

class RealmSigningLogCommand
{
    public function __construct(private readonly RealmSigningLogRepository $realmSigningLogRepository)
    {
    }

    public function revoke(RealmSigningLog $realmSigningLog): void
    {
        $this->realmSigningLogRepository->revoke($realmSigningLog, true);
    }

    /** @param array<int> $ids > */
    public function revokeBatch(array $ids): void
    {
        $lastId = end($ids);
        foreach ($ids as $id) {
            $this->realmSigningLogRepository->revokeById($id, $lastId === $id);
        }
    }
}
