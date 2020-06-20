<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Security\Authorization\Voter;

use Nadia\Bundle\NadiaSimpleSecurityBundle\Security\Authorization\Voter\SuperAdminRoleVoter;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\Doctrine\Entity\Role;
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

    /**
     * @dataProvider voteDataProvider
     *
     * @param array $roles
     * @param int $expectedResult
     */
    public function testVote(array $roles, $expectedResult)
    {
        $validSuperAdminRoles = ['ROLE_SUPER_ADMIN', 'ROLE_VIP_SUPER_ADMIN'];
        $voter = new SuperAdminRoleVoter($validSuperAdminRoles);
        $token = new UsernamePasswordToken('username', 'password', 'test', $roles);

        $this->assertEquals($expectedResult, $voter->vote($token, null, []));

        $legacyToken = new StubLegacyToken($roles);

        $this->assertEquals($expectedResult, $voter->vote($legacyToken, null, []));

        $legacyToken2 = new StubLegacyToken(array_map(function ($role) {
            return new Role($role);
        }, $roles));

        $this->assertEquals($expectedResult, $voter->vote($legacyToken2, null, []));
    }

    public function voteDataProvider()
    {
        return [
            [
                [],
                VoterInterface::ACCESS_ABSTAIN,
            ],
            [
                ['ROLE_NOT_SUPER_ADMIN', 'ROLE_USER', 'ROLE_ADMIN'],
                VoterInterface::ACCESS_ABSTAIN,
            ],
            [
                ['ROLE_NOT_SUPER_ADMIN', 'ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'],
                VoterInterface::ACCESS_GRANTED,
            ],
            [
                ['ROLE_NOT_SUPER_ADMIN', 'ROLE_USER', 'ROLE_ADMIN', 'ROLE_VIP_SUPER_ADMIN'],
                VoterInterface::ACCESS_GRANTED,
            ],
            [
                ['ROLE_NOT_SUPER_ADMIN', 'ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN', 'ROLE_VIP_SUPER_ADMIN'],
                VoterInterface::ACCESS_GRANTED,
            ],
        ];
    }
}
