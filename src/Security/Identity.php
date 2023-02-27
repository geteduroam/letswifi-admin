<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Security;

use App\Entity\Contact;
use Symfony\Component\Security\Core\User\UserInterface;

class Identity implements UserInterface
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
        return [];
    }

    public function getPassword(): string
    {
        return '';
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
}
