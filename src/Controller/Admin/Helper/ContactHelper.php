<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Controller\Admin\Helper;

use App\Entity\Contact;
use App\Repository\ContactRepository;

class ContactHelper
{
    public function __construct(private readonly ContactRepository $contactRepository)
    {
    }

    /**
     * @param array<Contact> $contacts
     *
     * @return array<string, Contact>
     */
    private function getContactAsStrings(array $contacts): array
    {
        $choices = [];

        foreach ($contacts as $contact) {
            $choices[$contact->getEmailAddress()] = $contact;
        }

        return $choices;
    }

    /** @return array<string, Contact> */
    public function getAllContacts(): array
    {
        $contacts = $this->contactRepository->findAll();

        return $this->getContactAsStrings($contacts);
    }
}
