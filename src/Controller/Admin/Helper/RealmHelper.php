<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Controller\Admin\Helper;

use App\Entity\Realm;
use App\Repository\RealmRepository;
use Symfony\Component\Security\Core\User\UserInterface;

use function count;

class RealmHelper
{
    public function __construct(private readonly RealmRepository $realmRepository)
    {
    }

    /**
     * @param array<Realm> $realms
     *
     * @return array<string>
     */
    private function getRealmsAsStrings(array $realms): array
    {
        $choices = [];

        foreach ($realms as $realm) {
            $choices[$realm->getRealm()] = $realm;
        }

        return $choices;
    }

    /** @return array<string> */
    public function getUserRealms(UserInterface $user): array
    {
        $realms = $this->realmRepository->findByUser($user->getId());

        $realmStrings = $this->getRealmsAsStrings($realms);

        if (count($realmStrings) === 0) {
            $realmStrings['no realms'] = 'no realms';
        }

        return $realmStrings;
    }

    /** @return array<string> */
    public function getAllRealms(): array
    {
        $realms = $this->realmRepository->findAll();

        return $this->getRealmsAsStrings($realms);
    }
}
