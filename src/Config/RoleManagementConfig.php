<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Config;

use Nadia\Bundle\NadiaSimpleSecurityBundle\Model\Role;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class RoleManagementConfig
 */
class RoleManagementConfig
{
    /**
     * @var string
     */
    private $firewallName;

    /**
     * @var string|null
     */
    private $objectManagerName;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var string
     */
    private $roleClassName;

    /**
     * @var array
     */
    private $roleGroups;

    /**
     * RoleManagementConfig constructor.
     *
     * @param string                $firewallName
     * @param string|null           $objectManagerName
     * @param UserProviderInterface $userProvider
     * @param string                $roleClassName
     * @param array                 $roleGroups
     */
    public function __construct(
        string $firewallName,
        ?string $objectManagerName,
        UserProviderInterface $userProvider,
        string $roleClassName,
        array $roleGroups
    ) {
        if ($roleClassName !== Role::class && !is_subclass_of($roleClassName, Role::class)) {
            throw new \InvalidArgumentException(
                sprintf('The role class "%s" must extend "%s"', $roleClassName, Role::class)
            );
        }

        $this->firewallName = $firewallName;
        $this->objectManagerName = $objectManagerName;
        $this->userProvider = $userProvider;
        $this->roleClassName = $roleClassName;
        $this->roleGroups = $roleGroups;
    }

    /**
     * @return string
     */
    public function getFirewallName(): string
    {
        return $this->firewallName;
    }

    /**
     * @return string|null
     */
    public function getObjectManagerName(): ?string
    {
        return $this->objectManagerName;
    }

    /**
     * @return UserProviderInterface
     */
    public function getUserProvider(): UserProviderInterface
    {
        return $this->userProvider;
    }

    /**
     * @return string
     */
    public function getRoleClassName(): string
    {
        return $this->roleClassName;
    }

    /**
     * @return array
     */
    public function getRoleGroups(): array
    {
        return $this->roleGroups;
    }
}
