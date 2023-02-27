<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Controller\Security\SamlBundle;

use Psr\Log\LoggerInterface;
use Surfnet\SamlBundle\Entity\IdentityProvider;
use Surfnet\SamlBundle\Entity\ServiceProvider;
use Surfnet\SamlBundle\Exception\LogicException;
use Surfnet\SamlBundle\Http\PostBinding;
use Surfnet\SamlBundle\Http\XMLResponse;
use Surfnet\SamlBundle\Metadata\MetadataFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SamlController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly MetadataFactory $metadataFactory,
        private readonly PostBinding $postBinding,
        private readonly IdentityProvider $identityProvider,
        private readonly ServiceProvider $serviceProvider,
    ) {
    }

    #[Route('/saml/acs', name: 'dashboard_saml_consume_assertion', methods: ['POST'])]
    public function consumeAssertionAction(Request $request): void
    {
        throw new LogicException(
            'Unreachable statement, should be handled by the SAML firewall',
        );
    }

    #[Route('/saml/metadata', name:'dashboard_saml_metadata', methods: ['GET'])]
    public function metadataAction(): XMLResponse
    {
        return new XMLResponse(
            $this->metadataFactory->generate(),
        );
    }
}
