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

use Nadia\Bundle\NadiaSimpleSecurityBundle\Model\Role;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

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
        if (version_compare(Kernel::VERSION, '4.3', '<')) {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('nadia_simple_security');
        } else {
            $treeBuilder = new TreeBuilder('nadia_simple_security');
            $rootNode = $treeBuilder->getRootNode();
        }

        $rootNode
            ->addDefaultsIfNotSet()
            ->fixXmlConfig('super_admin_role')
            ->fixXmlConfig('role_management')
            ->fixXmlConfig('route')
            ->children()
                ->arrayNode('super_admin_roles')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('role_managements')
                    ->arrayPrototype()
                        ->fixXmlConfig('role_group')
                        ->children()
                            ->scalarNode('firewall_name')->isRequired()->end()
                            ->scalarNode('object_manager_name')->defaultNull()->end()
                            ->scalarNode('user_provider')->isRequired()->end()
                            ->scalarNode('role_class')
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return !empty($v) && $v !== Role::class && !is_subclass_of($v, Role::class);
                                    })
                                    ->thenInvalid('The role class %s must extend "' . Role::class . '"')
                                ->end()
                                ->defaultValue('')
                            ->end()
                            ->arrayNode('role_groups')
                                ->arrayPrototype()
                                    ->fixXmlConfig('role')
                                    ->children()
                                        ->scalarNode('title')->isRequired()->end()
                                        ->arrayNode('roles')
                                            ->arrayPrototype()
                                                ->children()
                                                    ->scalarNode('role')->isRequired()->end()
                                                    ->scalarNode('title')->isRequired()->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('routes')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('target_class_name')->isRequired()->end()
                            ->scalarNode('route_name')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
