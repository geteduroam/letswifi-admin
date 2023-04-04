<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Security\Voter;

use App\Entity\Contact;
use App\Entity\Realm;
use App\Entity\RealmSigningLog;
use App\Entity\RealmSigningUser;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function assert;
use function in_array;

class RealmSigningLogVoter extends Voter
{
    public const EDIT = 'edit';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        return ($subject instanceof Realm ||
                $subject instanceof RealmSigningLog ||
                $subject instanceof RealmSigningUser)
            && $attribute === self::EDIT;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Contact) {
            return false;
        }

        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return true;
        }

        assert(
            $subject instanceof RealmSigningLog ||
            $subject instanceof RealmSigningUser ||
            $subject instanceof Realm,
        );

        if ($subject instanceof RealmSigningLog) {
            return match ($attribute) {
                self::EDIT => $this->canEdit($subject, $user),
                default => throw new LogicException('This code should not be reached!')
            };
        }

        if ($subject instanceof RealmSigningUser) {
            return match ($attribute) {
                self::EDIT => $this->canEditRealmSigningUser($subject, $user),
                default => throw new LogicException('This code should not be reached!')
            };
        }

        return match ($attribute) {
            self::EDIT => $this->canEditRealm($subject, $user),
            default => throw new LogicException('This code should not be reached!')
        };
    }

    private function canEdit(RealmSigningLog $realmSigningLog, Contact $user): bool
    {
        return $user->getSuperAdmin() || $user->isOwnerOfRealm($realmSigningLog->getRealm());
    }

    private function canEditRealmSigningUser(RealmSigningLog $realmSigningUser, Contact $user): bool
    {
        return $user->getSuperAdmin() || $user->isOwnerOfRealm($realmSigningUser->getRealm());
    }

    private function canEditRealm(Realm $realm, Contact $user): bool
    {
        return $user->getSuperAdmin() || $user->isOwnerOfRealm($realm);
    }
}
