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

use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\Doctrine\Entity\Role;
use PHPUnit\Framework\TestCase;

/**
 * Class Role
 */
class RoleTest extends TestCase
{
    public function testAll()
    {
        $role = new Role('ROLE_TESTER');

        $this->assertEquals('ROLE_TESTER', $role->getRole());
        $this->assertEquals('ROLE_TESTER', (string) $role);
        $this->assertEquals(0, $role->getId());

        $idProperty = (new \ReflectionClass($role))->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($role, 100);

        $this->assertEquals(100, $role->getId());
    }
}
