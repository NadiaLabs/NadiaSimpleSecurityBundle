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

use Nadia\Bundle\NadiaSimpleSecurityBundle\Config\RoleManagementConfig;
use Nadia\Bundle\NadiaSimpleSecurityBundle\DependencyInjection\Container\ServiceProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class NadiaSimpleSecurityExtension
 */
class NadiaSimpleSecurityExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration(Kernel::VERSION);
        $config = $this->processConfiguration($configuration, $configs);

        $this->registerParameterBagService($container);
        $this->registerRoleManagementConfigServiceProvider($container, $config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/'));

        $loader->load('commands.yml');
        $loader->load('controllers.yml');
    }

    /**
     * @inheritdoc
     */
    public function getNamespace()
    {
        return 'http://nadialabs.com.tw/schema/dic/simple-security';
    }

    /**
     * @param ContainerBuilder $container
     */
    private function registerParameterBagService(ContainerBuilder $container)
    {
        if ($container->has('parameter_bag')) {
            $container->setAlias('nadia.simple_security.parameter_bag', new Alias('parameter_bag', false));
        } else {
            $definition = new Definition(ParameterBag::class);

            $definition->setFactory([new Reference('service_container'), 'getParameterBag']);

            $container->setDefinition('nadia.simple_security.parameter_bag', $definition);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function registerRoleManagementConfigServiceProvider(ContainerBuilder $container, array $config)
    {
        $idPrefix = 'nadia.simple_security.role_management_config.';
        $definition = new Definition(ServiceProvider::class, [new Reference('service_container'), $idPrefix]);

        foreach ($config['role_managements'] as $roleManagement) {
            $id = $idPrefix . $roleManagement['firewall_name'];

            $container->setDefinition($id, new Definition(RoleManagementConfig::class, [
                $roleManagement['firewall_name'],
                $roleManagement['object_manager_name'],
                new Reference($roleManagement['user_provider']),
                $roleManagement['role_class'],
                $roleManagement['role_groups'],
            ]));
        }

        $container->setDefinition('nadia.simple_security.service_provider.role_management_config', $definition);
    }
}
