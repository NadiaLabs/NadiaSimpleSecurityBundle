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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Trait RoleEditableTrait
 */
trait RoleEditableTrait
{
    /**
     * @return string
     */
    protected function getRolesPropertyName()
    {
        return 'roles';
    }

    /**
     * @return string[]|object[]
     */
    public function getRoles()
    {
        return $this->{$this->getRolesPropertyName()};
    }

    /**
     * @param string|object $role
     *
     * @return $this
     */
    public function addRole($role)
    {
        $roles = &$this->{$this->getRolesPropertyName()};

        if (!$this->hasRole($role)) {
            if ($roles instanceof Collection) {
                $roles->add($role);
            } else {
                $roles[] = $role;
            }
        }

        return $this;
    }

    /**
     * @param string|object $role
     *
     * @return $this
     */
    public function removeRole($role)
    {
        $roles = &$this->{$this->getRolesPropertyName()};

        if ($roles instanceof Collection) {
            $roles->removeElement($role);
        } elseif (false !== $key = array_search($role, $roles, true)) {
            unset($roles[$key]);

            $roles = array_values($roles);
        }

        return $this;
    }

    /**
     * @param string[]|object[] $roles
     *
     * @return $this
     */
    public function setRoles(array $roles)
    {
        $currentRoles = &$this->{$this->getRolesPropertyName()};

        if ($currentRoles instanceof Collection) {
            $currentRoles = new ArrayCollection();
        } else {
            $currentRoles = [];
        }

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * @param string|object $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        $roles = &$this->{$this->getRolesPropertyName()};

        if ($roles instanceof Collection) {
            return $roles->contains($role);
        }

        return in_array($role, $roles, true);
    }
}
