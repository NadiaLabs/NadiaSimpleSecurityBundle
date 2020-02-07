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

use Nadia\Bundle\NadiaSimpleSecurityBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class ConfigurationTest
 */
class ConfigurationTest extends TestCase
{
    public function testDefaultConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), []);
        $expectConfig = [
            'super_admin_roles' => [],
            'role_managements' => [],
        ];

        $this->assertEquals($expectConfig, $config);
    }

    public function testRoleManagements()
    {
        $expectConfig = [
            'super_admin_roles' => [],
            'role_managements' => require __DIR__ . '/../Fixtures/config/test-role-managements.php',
        ];
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [$expectConfig]);

        $this->assertEquals($expectConfig, $config);
    }

    public function testSuperAdminRoles()
    {
        $expectConfig = [
            'super_admin_roles' => [
                'ROLE_SUPER_ADMIN',
                'ROLE_VIP_SUPER_ADMIN',
            ],
            'role_managements' => [],
        ];
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [$expectConfig]);

        $this->assertEquals($expectConfig, $config);
    }

    public function testInvalidRoleClass()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = [
            'role_managements' => [
                'firewall_name' => 'main',
                'user_provider' => 'test.user_provider',
                'role_class' => 'Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\Doctrine\Entity\InvalidRole',
                'role_groups' => []
            ],
        ];
        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [$config]);
    }
}
