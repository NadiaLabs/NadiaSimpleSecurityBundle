<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\RoleEditable;

use Nadia\Bundle\NadiaSimpleSecurityBundle\Model\RoleEditableInterface;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Model\RoleEditableTrait;

/**
 * Class User1
 */
class User1 implements RoleEditableInterface
{
    use RoleEditableTrait;

    protected $roles = [];
}
