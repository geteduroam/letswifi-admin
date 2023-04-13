<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Security\SamlBundle;

use App\Entity\Contact;
use App\Entity\Realm;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Identity implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(private readonly Contact $contact)
    {
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    /** @return array|string[] */
    public function getRoles(): array
    {
        return $this->contact->getRoles();
    }

    public function getPassword(): string
    {
        return $this->contact->getPassword();
    }

    public function eraseCredentials(): void
    {
    }

    public function getUsername(): string
    {
        return $this->contact->getNameId();
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function getId(): int
    {
        return $this->getContact()->getId();
    }

    public function getSuperAdmin(): bool
    {
        return $this->getContact()->getSuperAdmin();
    }

    public function isOwnerOfRealm(Realm $realm): bool
    {
        return $this->getContact()->isOwnerOfRealm($realm);
    }
}
