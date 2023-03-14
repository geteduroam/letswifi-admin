<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Security\SamlBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DashboardSamlExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(
            __DIR__ . '/../Resources/config',
        ));
        $loader->load('services.yml');

        $container->setParameter(
            'surfnet.dashboard.security.authentication.administrator_teams',
            $config['administrator_teams'],
        );
        $container->setParameter(
            'surfnet.dashboard.security.authentication.session.maximum_absolute_lifetime_in_seconds',
            $config['session_lifetimes']['max_absolute_lifetime'],
        );
        $container->setParameter(
            'surfnet.dashboard.security.authentication.session.maximum_relative_lifetime_in_seconds',
            $config['session_lifetimes']['max_relative_lifetime'],
        );
    }
}
