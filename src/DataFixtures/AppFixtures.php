<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\DataFixtures;

use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $superAdmin = new Contact();
        $superAdmin->setNameId('super');
        $superAdmin->setDisplayName('Super');
        $superAdmin->setEmailAddress('super@super.nl');
        $superAdmin->setPassword('$2y$13$zQb6nVLleImMZgvrDXyVk.v7Wdwn9IsaA/O.Rceci2ipIAYfrS6M6');
        $superAdmin->setSuperAdmin(true);

        $manager->persist($superAdmin);
        $manager->flush();
    }
}
