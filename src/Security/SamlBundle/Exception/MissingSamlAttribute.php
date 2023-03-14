<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Security\SamlBundle\Exception;

use Surfnet\SamlBundle\Security\Exception\RuntimeException;

class MissingSamlAttribute extends RuntimeException
{
}
