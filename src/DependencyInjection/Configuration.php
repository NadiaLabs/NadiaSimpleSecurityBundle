<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('nadia_simple_security');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->fixXmlConfig('role_management')
            ->children()
                ->arrayNode('role_managements')
                    ->arrayPrototype()
                        ->fixXmlConfig('group')
                        ->children()
                            ->scalarNode('firewall_name')->end()
                            ->arrayNode('groups')
                                ->useAttributeAsKey('title')
                                ->arrayPrototype()
                                    ->beforeNormalization()
                                        ->ifTrue(function ($v) {
                                            return is_array($v)
                                                && !empty($v['role'])
                                                && !empty($v['role']['name'])
                                                && !empty($v['role']['value']);
                                        })
                                        ->then(function ($v) {
                                            return [$v['role']['name'] => $v['role']['value']];
                                        })
                                        ->ifTrue(function ($v) {
                                            return is_array($v)
                                                && !empty($v['role'])
                                                && !empty($v['role'][0])
                                                && !empty($v['role'][0]['name'])
                                                && !empty($v['role'][0]['value']);
                                        })
                                        ->then(function ($v) {
                                            return array_combine(
                                                array_column($v['role'], 'name'),
                                                array_column($v['role'], 'value')
                                            );
                                        })
                                    ->end()
                                    ->useAttributeAsKey('name')
                                    ->scalarPrototype()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
