<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\DependencyInjection\Container;

use Nadia\Bundle\NadiaSimpleSecurityBundle\Config\RoleManagementConfig;
use Nadia\Bundle\NadiaSimpleSecurityBundle\DependencyInjection\Container\ServiceProvider;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\Doctrine\Entity\Role;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\TestUserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class ServiceProviderTest
 */
class ServiceProviderTest extends TestCase
{
    /**
     * @dataProvider getTestData
     *
     * @param ServiceProvider      $serviceProvider
     * @param RoleManagementConfig $roleManagerConfig
     */
    public function testAll($serviceProvider, $roleManagerConfig)
    {
        $this->assertEquals($roleManagerConfig, $serviceProvider->get('test'));
        $this->assertEquals(true, $serviceProvider->has('test'));
        $this->assertEquals(false, $serviceProvider->has('invalid'));
    }

    /**
     * @dataProvider getTestData
     *
     * @param ServiceProvider $serviceProvider
     */
    public function testInvalidFirewallName($serviceProvider)
    {
        $this->expectException('InvalidArgumentException');

        $serviceProvider->get('invalid');
    }

    public function testConstructor()
    {
        $container = new Container();

        $serviceProvider = new ServiceProvider($container);
        $ref = new \ReflectionClass($serviceProvider);

        $containerProperty = $ref->getProperty('container');
        $containerProperty->setAccessible(true);

        $this->assertEquals($container, $containerProperty->getValue($serviceProvider));
    }

    public function getTestData()
    {
        $roleManagerConfig = new RoleManagementConfig('test', null, new TestUserProvider(), Role::class, []);
        $container = new Container();

        $container->set('test', $roleManagerConfig);

        return [
            [new ServiceProvider($container), $roleManagerConfig],
        ];
    }
}
