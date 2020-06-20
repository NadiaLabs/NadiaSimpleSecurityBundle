<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\DependencyInjection;

use Nadia\Bundle\NadiaSimpleSecurityBundle\Config\RoleManagementConfig;
use Nadia\Bundle\NadiaSimpleSecurityBundle\DependencyInjection\Container\ServiceProvider;
use Nadia\Bundle\NadiaSimpleSecurityBundle\DependencyInjection\NadiaSimpleSecurityExtension;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Routing\Generator\EditRolesUrlGenerator;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Security\Authorization\Voter\SuperAdminRoleVoter;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\TestUserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class NadiaSimpleSecurityExtensionTest
 */
abstract class NadiaSimpleSecurityExtensionTest extends TestCase
{
    /**
     * @param ContainerBuilder $container
     * @param string           $filename  Filename without extension part (e.g. "test" for test.php/test.xml/test.yml)
     */
    abstract protected function loadConfigFile(ContainerBuilder $container, $filename);

    public function testSuperAdminRoleVoter()
    {
        $container = $this->createContainerByConfigFile('test');
        $definition = $container->getDefinition(SuperAdminRoleVoter::class);

        $this->assertEquals(
            ['ROLE_SUPER_ADMIN', 'ROLE_VIP_SUPER_ADMIN'],
            $container->getParameter('nadia.simple_security.super_admin_roles')
        );

        $this->assertEquals(SuperAdminRoleVoter::class, $definition->getClass());
        $this->assertTrue($definition->hasTag('security.voter'));
    }

    public function testAliases()
    {
        $container = $this->createContainerByConfigFile('test');

        $this->assertTrue($container->has('nadia.simple_security.parameter_bag'));
        $this->assertEquals(
            ParameterBagInterface::class,
            $container->getDefinition('nadia.simple_security.parameter_bag')->getClass()
        );

        $this->assertTrue($container->has(ServiceProvider::class));
        $this->assertEquals(
            ServiceProvider::class,
            $container->getDefinition(ServiceProvider::class)->getClass()
        );
        $this->assertInstanceOf(
            RoleManagementConfig::class,
            $container->get('test.service_provider.role_management_config')->get('main')
        );
        $this->assertInstanceOf(
            RoleManagementConfig::class,
            $container->get('test.service_provider.role_management_config')->get('test')
        );
    }

    public function testEditRolesUrlGenerator()
    {
        $container = $this->createContainerByConfigFile('test');
        $def = $container->getDefinition(EditRolesUrlGenerator::class);
        $originalRouteMap = require __DIR__ . '/../Fixtures/config/test-routes.php';

        $this->assertEquals(
            array_column($originalRouteMap, 'target_class_name'),
            array_keys($def->getArgument(1))
        );
        $this->assertEquals(
            array_column($originalRouteMap, 'route_name'),
            array_values($def->getArgument(1))
        );
    }

    protected function createContainerByConfigFile($filename, array $data = [])
    {
        $container = $this->createBaseContainer($data);

        $container->registerExtension(new NadiaSimpleSecurityExtension());

        $this->loadConfigFile($container, $filename);

        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);

        $container->setDefinition('test.user_provider', new Definition(TestUserProvider::class));
        $container->setDefinition('test.user_provider2', new Definition(TestUserProvider::class));
        $container->setAlias(
            'test.service_provider.role_management_config',
            new Alias(ServiceProvider::class, true)
        );

        $container->compile();

        return $container;
    }

    /**
     * @param array $data
     *
     * @return ContainerBuilder
     */
    protected function createBaseContainer(array $data = [])
    {
        // Make sure cache directory is different
        sleep(1);

        return new ContainerBuilder(new ParameterBag(array_merge([
            'kernel.bundles' => [
                'NadiaSimpleSecurityBundle' => 'Nadia\\Bundle\\NadiaSimpleSecurityBundle\\NadiaSimpleSecurityBundle',
            ],
            'kernel.bundles_metadata' => [
                'NadiaSimpleSecurityBundle' => [
                    'namespace' => 'Nadia\\Bundle\\NadiaSimpleSecurityBundle',
                    'path' => __DIR__ . '/../..',
                ],
            ],
            'kernel.cache_dir' => sys_get_temp_dir() . '/nadia-simple-security-bundle-tests-' . time(),
            'kernel.project_dir' => __DIR__,
            'kernel.debug' => false,
            'kernel.environment' => 'test',
            'kernel.name' => 'kernel',
            'kernel.root_dir' => __DIR__,
            'kernel.container_class' => 'testContainer',
        ], $data)));
    }
}
