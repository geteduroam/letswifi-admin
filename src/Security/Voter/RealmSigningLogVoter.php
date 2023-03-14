<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Security\Voter;

use App\Entity\Contact;
use App\Entity\RealmSigningLog;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function assert;

class RealmSigningLogVoter extends Voter
{
    public const EDIT = 'edit';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        return $subject instanceof RealmSigningLog && $attribute === self::EDIT;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Contact) {
            return false;
        }

        assert($subject instanceof RealmSigningLog);

        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            default => throw new LogicException('This code should not be reached!')
        };
    }

    private function canEdit(RealmSigningLog $realmSigningLog, Contact $user): bool
    {
        return $user->getSuperAdmin() || $user->isOwnerOfRealm($realmSigningLog->getRealm());
    }
}
