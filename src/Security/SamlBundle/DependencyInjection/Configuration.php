<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Security\SamlBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use function is_int;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('dashboard_saml');
        $rootNode    = $treeBuilder->getRootNode();
        $childNodes  = $rootNode->children();
        $this->appendSessionConfiguration($childNodes);

        return $treeBuilder;
    }

    private function appendSessionConfiguration(NodeBuilder $childNodes): void
    {
        $childNodes
            ->scalarNode('administrator_teams')
                ->info('All users in these teams get the administrator role. Teams is a string containing roles ' .
                    'seperated by comma\'s')
                ->isRequired()
            ->end()
            ->arrayNode('session_lifetimes')
                ->isRequired()
                ->children()
                    ->integerNode('max_absolute_lifetime')
                        ->isRequired()
                        ->defaultValue(3600)
                        ->info('The maximum lifetime of a session regardless of interaction by the user, ' .
                            'in seconds.')
                        ->example('3600 -> 1 hour * 60 minutes * 60 seconds')
                        ->validate()
                            ->ifTrue(
                                static function ($lifetime) {
                                    return !is_int($lifetime);
                                },
                            )
                            ->thenInvalid('max_absolute_lifetime must be an integer')
                        ->end()
                    ->end()
                    ->integerNode('max_relative_lifetime')
                        ->isRequired()
                        ->defaultValue(600)
                        ->info(
                            'The maximum relative lifetime of a session; the maximum allowed time between two '
                            . 'interactions by the user',
                        )
                        ->example('600 -> 10 minutes * 60 seconds')
                        ->validate()
                            ->ifTrue(
                                static function ($lifetime) {
                                    return !is_int($lifetime);
                                },
                            )
                            ->thenInvalid('max_relative_lifetime must be an integer')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
