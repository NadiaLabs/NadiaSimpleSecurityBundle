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
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class ConfigurationTest
 */
class ConfigurationTest extends TestCase
{
    /**
     * @dataProvider gerKernelVersions
     *
     * @param string $kernelVersion
     */
    public function testDefaultConfig($kernelVersion)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration($kernelVersion), []);
        $expectConfig = [
            'role_managements' => [],
        ];

        $this->assertEquals($expectConfig, $config);
    }

    /**
     * @dataProvider gerKernelVersions
     *
     * @param string $kernelVersion
     */
    public function testRoleManagements($kernelVersion)
    {
        $expectConfig = [
            'role_managements' => require __DIR__ . '/../Fixtures/config/test-role-managements.php',
        ];
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration($kernelVersion), [$expectConfig]);

        $this->assertEquals($expectConfig, $config);
    }

    /**
     * @dataProvider gerKernelVersions
     *
     * @param string $kernelVersion
     */
    public function testInvalidRoleClass($kernelVersion)
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
        $processor->processConfiguration(new Configuration($kernelVersion), [$config]);
    }

    public function gerKernelVersions()
    {
        return [
            ['3.4'],
            ['4.2'],
            ['4.3'],
            [Kernel::VERSION],
        ];
    }
}
