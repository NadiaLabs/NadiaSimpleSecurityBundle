<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Config;

use Nadia\Bundle\NadiaSimpleSecurityBundle\Config\RoleManagementConfig;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\Doctrine\Entity\InvalidRole;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\Doctrine\Entity\Role;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\TestUserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class RoleManagementConfigTest
 */
class RoleManagementConfigTest extends TestCase
{
    /**
     * @dataProvider getTestGeneralCaseData
     *
     * @param string                $firewallName
     * @param string                $objectManagerName
     * @param UserProviderInterface $userProvider
     * @param string                $roleClassName
     * @param array                 $roleGroups
     */
    public function testGeneralCase($firewallName, $objectManagerName, $userProvider, $roleClassName, $roleGroups)
    {
        $config = new RoleManagementConfig(
            $firewallName,
            $objectManagerName,
            $userProvider,
            $roleClassName,
            $roleGroups
        );

        $this->assertEquals($firewallName, $config->getFirewallName());
        $this->assertEquals($objectManagerName, $config->getObjectManagerName());
        $this->assertEquals($userProvider, $config->getUserProvider());
        $this->assertEquals($roleClassName, $config->getRoleClassName());
        $this->assertEquals($roleGroups, $config->getRoleGroups());
    }

    public function testInvalidRoleClassName()
    {
        $this->expectException('InvalidArgumentException');

        new RoleManagementConfig('test', null, new TestUserProvider(), InvalidRole::class, []);
    }

    public function getTestGeneralCaseData()
    {
        $roleManagements = require __DIR__ . '/../Fixtures/config/test-role-managements.php';

        return [
            [
                'test',
                null,
                new TestUserProvider(),
                Role::class,
                $roleManagements[0]['role_groups'],
            ],
            [
                'test',
                null,
                new TestUserProvider(),
                Role::class,
                [],
            ],
            [
                'test',
                'default',
                new TestUserProvider(),
                Role::class,
                $roleManagements[0]['role_groups'],
            ]
        ];
    }
}
