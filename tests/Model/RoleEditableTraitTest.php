<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Model\RoleEditableInterface;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\Doctrine\Entity\Role;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\RoleEditable\User1;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\RoleEditable\User2;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\RoleEditable\User3;
use PHPUnit\Framework\TestCase;

/**
 * Trait RoleEditableTrait
 */
class RoleEditableTraitTest extends TestCase
{
    /**
     * @dataProvider getTestAddRoleData
     *
     * @param RoleEditableInterface $user
     * @param string[]|object[]     $roles
     */
    public function testAddRole(RoleEditableInterface $user, $roles)
    {
        foreach ($roles as $role) {
            $user->addRole($role);
        }

        $this->assertEquals($roles, $user->getRoles());
    }

    /**
     * @dataProvider getTestAddRoleData
     *
     * @param RoleEditableInterface $user
     * @param string[]|object[]     $roles
     */
    public function testSetRole(RoleEditableInterface $user, $roles)
    {
        $user->setRoles($roles);

        $this->assertEquals($roles, $user->getRoles());
    }

    /**
     * @dataProvider getTestHasRoleData
     *
     * @param RoleEditableInterface $user
     * @param string[]|object[]     $roles
     * @param string|object         $missingRole
     */
    public function testHasRole(RoleEditableInterface $user, $roles, $missingRole)
    {
        $targetRole = $roles[1];

        $user->setRoles($roles);

        $this->assertEquals(true, $user->hasRole($targetRole));
        $this->assertEquals(false, $user->hasRole($missingRole));
    }

    /**
     * @dataProvider getTestHasRoleData
     *
     * @param RoleEditableInterface $user
     * @param string[]|object[]     $roles
     * @param string|object         $missingRole
     */
    public function testRemoveRole(RoleEditableInterface $user, $roles, $missingRole)
    {
        $removedRole = $roles[1];
        $expectRoles = [$roles[0], $roles[2]];

        $user->setRoles($roles);
        $user->removeRole($removedRole);

        $index = 0;
        foreach ($user->getRoles() as $currentRole) {
            $this->assertEquals($expectRoles[$index++], $currentRole);
        }

        $user->removeRole($missingRole);

        $index = 0;
        foreach ($user->getRoles() as $currentRole) {
            $this->assertEquals($expectRoles[$index++], $currentRole);
        }
    }

    public function getTestAddRoleData()
    {
        $stringRoles = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];
        $objectRoles = [new Role('ROLE_USER'), new Role('ROLE_ADMIN'), new Role('ROLE_SUPER_ADMIN')];
        $arrayCollectionStringRoles = new ArrayCollection($stringRoles);
        $arrayCollectionObjectRoles = new ArrayCollection($objectRoles);

        return [
            [new User1(), $stringRoles],
            [new User1(), $objectRoles],
            [new User2(), $stringRoles],
            [new User2(), $objectRoles],
            [new User3(), $arrayCollectionStringRoles],
            [new User3(), $arrayCollectionObjectRoles],
        ];
    }

    public function getTestHasRoleData()
    {
        $data = [];
        $users = [new User1(), new User2()];
        $missingStringRole = 'ROLE_MISSING';
        $missingObjectRole = new Role($missingStringRole);

        foreach ($users as $user) {
            $stringRoles = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];
            $objectRoles = [new Role('ROLE_USER'), new Role('ROLE_ADMIN'), new Role('ROLE_SUPER_ADMIN')];

            $data[] = [$user, $stringRoles, $missingStringRole];
            $data[] = [clone $user, $objectRoles, $missingObjectRole];
        }

        $stringRoles = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];
        $objectRoles = [new Role('ROLE_USER'), new Role('ROLE_ADMIN'), new Role('ROLE_SUPER_ADMIN')];
        $arrayCollectionStringRoles = new ArrayCollection($stringRoles);
        $arrayCollectionObjectRoles = new ArrayCollection($objectRoles);

        $data[] = [new User3(), $arrayCollectionStringRoles, $missingStringRole];
        $data[] = [new User3(), $arrayCollectionObjectRoles, $missingObjectRole];

        return $data;
    }
}
