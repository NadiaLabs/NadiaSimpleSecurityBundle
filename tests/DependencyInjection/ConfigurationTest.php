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
            'role_class' => '',
            'role_managements' => [],
        ];

        $this->assertEquals($expectConfig, $config);
    }

    public function testRoleManagements()
    {
        $expectConfig = [
            'role_class' => 'Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\Doctrine\Entity\Role',
            'role_managements' => require __DIR__ . '/../Fixtures/config/test-role-managements.php',
        ];
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [$expectConfig]);

        $this->assertEquals($expectConfig, $config);
    }

    public function testInvalidRoleClass()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = [
            'role_class' => 'Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\Doctrine\Entity\InvalidRole',
        ];
        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [$config]);
    }
}
