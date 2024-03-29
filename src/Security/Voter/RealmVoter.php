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
use App\Security\SamlBundle\Identity;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

use function assert;
use function in_array;

class RealmVoter extends Voter
{
    public const EDIT = 'edit';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        return ($subject instanceof Realm)
            && $attribute === self::EDIT;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!($user instanceof UserInterface)) {
            return false;
        }

        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        assert(
            $subject instanceof Realm,
        );

        return match ($attribute) {
            self::EDIT => $this->canEditRealm($subject, $user),
            default => throw new LogicException('This code should not be reached!')
        };
    }

    private function canEditRealm(Realm $realm, UserInterface $user): bool
    {
        if (!$user instanceof Identity && !$user instanceof Contact) {
            return false;
        }

        return $user->getSuperAdmin() || $user->isOwnerOfRealm($realm);
    }
}
