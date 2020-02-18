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
 * Class Role
 */
abstract class Role
{
    /**
     * @var string
     */
    protected $role;

    /**
     * Role constructor.
     *
     * @param string $role
     */
    public function __construct(string $role)
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    abstract public function getId();

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->role;
    }
}
