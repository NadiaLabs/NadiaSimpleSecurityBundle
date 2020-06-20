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
use Nadia\Bundle\NadiaSimpleSecurityBundle\Routing\Generator\EditRolesUrlGenerator;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Security\Authorization\Voter\SuperAdminRoleVoter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/'));

        $loader->load('services.yml');
        $loader->load('commands.yml');
        $loader->load('controllers.yml');

        $this->registerParameterBagService($container);
        $this->registerRoleManagementConfigServiceProvider($container, $config);
        $this->registerObjectManagerNameParameter($container, $config);

        if (!empty($config['super_admin_roles'])) {
            $this->registerSuperAdminVoterService($container, $config['super_admin_roles']);
        }

        if (!empty($config['routes'])) {
            $this->modifyEditRolesUrlGenerator($container, $config['routes']);
        }
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
        $definition = (new Definition(ParameterBagInterface::class))
            ->setFactory([new Reference('service_container'), 'getParameterBag'])
            ->setPublic(false);

        $container->setDefinition('nadia.simple_security.parameter_bag', $definition);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function registerRoleManagementConfigServiceProvider(ContainerBuilder $container, array $config)
    {
        $idPrefix = 'nadia.simple_security.role_management_config.';
        $serviceMap = [];

        foreach ($config['role_managements'] as $roleManagement) {
            $id = $idPrefix . $roleManagement['firewall_name'];
            $configDefinition = new Definition(RoleManagementConfig::class, [
                $roleManagement['firewall_name'],
                $roleManagement['object_manager_name'],
                new Reference($roleManagement['user_provider']),
                $roleManagement['role_class'],
                $roleManagement['role_groups'],
            ]);

            $container->setDefinition($id, $configDefinition);

            $serviceMap[$roleManagement['firewall_name']] = new Reference($id);
        }

        $container->getDefinition(ServiceProvider::class)
            ->replaceArgument(0, ServiceLocatorTagPass::register($container, $serviceMap));
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function registerObjectManagerNameParameter(ContainerBuilder $container, array $config)
    {
        $parameterId = 'nadia.simple_security.object_manager_names';
        $objectManagerNames = [];

        foreach ($config['role_managements'] as $roleManagement) {
            $objectManagerNames[] = empty($roleManagement['object_manager_name'])
                ? 'default'
                : $roleManagement['object_manager_name']
            ;
        }

        if (!empty($objectManagerNames)) {
            $objectManagerNames = array_unique($objectManagerNames);

            $container->setParameter($parameterId, $objectManagerNames);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string[]         $superAdminRoles
     */
    private function registerSuperAdminVoterService(ContainerBuilder $container, array $superAdminRoles)
    {
        $parameterId = 'nadia.simple_security.super_admin_roles';
        $container->setParameter($parameterId, $superAdminRoles);

        $definition = new Definition(SuperAdminRoleVoter::class, [new Parameter($parameterId)]);

        $definition->addTag('security.voter', ['priority' => 250]);

        $container->setDefinition(SuperAdminRoleVoter::class, $definition);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $routes
     */
    private function modifyEditRolesUrlGenerator(ContainerBuilder $container, array $routes)
    {
        $routeMap = [];

        foreach ($routes as $route) {
            $routeMap[$route['target_class_name']] = $route['route_name'];
        }

        $container->getDefinition(EditRolesUrlGenerator::class)
            ->replaceArgument(1, $routeMap);
    }
}
