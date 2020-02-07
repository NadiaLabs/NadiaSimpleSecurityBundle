<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Security\Authorization\Voter;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class SuperAdminRoleVoterTest
 */
class SuperAdminRoleVoterTest extends TestCase
{
    public function testConstructor()
    {
        $validSuperAdminRoles = [];
        $voter = new SuperAdminRoleVoter($validSuperAdminRoles);
        $property = (new \ReflectionClass($voter))->getProperty('validSuperAdminRoles');
        $property->setAccessible(true);
        $this->assertEquals($validSuperAdminRoles, $property->getValue($voter));

        $validSuperAdminRoles = ['ROLE_SUPER_ADMIN', 'ROLE_VIP_SUPER_ADMIN'];
        $voter = new SuperAdminRoleVoter($validSuperAdminRoles);
        $property = (new \ReflectionClass($voter))->getProperty('validSuperAdminRoles');
        $property->setAccessible(true);
        $this->assertEquals($validSuperAdminRoles, $property->getValue($voter));
    }

    public function testVote()
    {
        $validSuperAdminRoles = ['ROLE_SUPER_ADMIN', 'ROLE_VIP_SUPER_ADMIN'];
        $voter = new SuperAdminRoleVoter($validSuperAdminRoles);

        $roles = [];
        $token = new UsernamePasswordToken('username', 'password', 'test', $roles);

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $voter->vote($token, null, []));

        $roles = ['ROLE_NOT_SUPER_ADMIN', 'ROLE_USER', 'ROLE_ADMIN'];
        $token = new UsernamePasswordToken('username', 'password', 'test', $roles);

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $voter->vote($token, null, []));

        $roles = ['ROLE_NOT_SUPER_ADMIN', 'ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];
        $token = new UsernamePasswordToken('username', 'password', 'test', $roles);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, null, []));

        $roles = ['ROLE_NOT_SUPER_ADMIN', 'ROLE_USER', 'ROLE_ADMIN', 'ROLE_VIP_SUPER_ADMIN'];
        $token = new UsernamePasswordToken('username', 'password', 'test', $roles);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, null, []));

        $roles = ['ROLE_NOT_SUPER_ADMIN', 'ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN', 'ROLE_VIP_SUPER_ADMIN'];
        $token = new UsernamePasswordToken('username', 'password', 'test', $roles);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, null, []));
    }
}
