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

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class StubUrlGenerator
 * @method array __serialize()
 * @method void __unserialize(array $data)
 * @method string[] getRoleNames()
 */
class StubLegacyToken implements TokenInterface
{
    private $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function serialize()
    {
    }

    public function unserialize($serialized)
    {
    }

    public function __toString()
    {
    }

    public function getCredentials()
    {
    }

    public function getUser()
    {
    }

    public function setUser($user)
    {
    }

    public function getUsername()
    {
    }

    public function isAuthenticated()
    {
    }

    public function setAuthenticated($isAuthenticated)
    {
    }

    public function eraseCredentials()
    {
    }

    public function getAttributes()
    {
    }

    public function setAttributes(array $attributes)
    {
    }

    public function hasAttribute($name)
    {
    }

    public function getAttribute($name)
    {
    }

    public function setAttribute($name, $value)
    {
    }

    public function __call($name, $arguments)
    {
    }
}
