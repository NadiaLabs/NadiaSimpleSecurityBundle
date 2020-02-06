<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Model;

/**
 * Interface RoleEditableInterface
 */
interface RoleEditableInterface
{
    /**
     * @return string[]|object[]
     */
    public function getRoles();

    /**
     * @param string|object $role
     */
    public function addRole($role);

    /**
     * @param string|object $role
     */
    public function removeRole($role);

    /**
     * @param string[]|object[] $roles
     */
    public function setRoles($roles);

    /**
     * @param string|object $role
     */
    public function hasRole($role);
}
