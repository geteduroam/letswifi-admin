<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Entity;

use App\Repository\RealmContactRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RealmContactRepository::class)]
class RealmContact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\ManyToOne(targetEntity: Contact::class, inversedBy: 'realmContacts')]
    private Contact $contact;

    #[ORM\ManyToOne(targetEntity: Realm::class, inversedBy: 'realmContacts')]
    #[ORM\JoinColumn(name: 'realm', referencedColumnName: 'realm', nullable: false)]
    private Realm $realm;

    public function __construct()
    {
        $this->contact = new Contact();
        $this->realm   = new Realm();
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getRealm(): Realm
    {
        return $this->realm;
    }

    public function setRealm(Realm $realm): self
    {
        $this->realm = $realm;

        return $this;
    }

    public function getContact(): Contact|null
    {
        return $this->contact;
    }

    public function setContact(Contact|null $contact): self
    {
        $this->contact = $contact;

        return $this;
    }
}
