<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\DependencyInjection\Container;

use Nadia\Bundle\NadiaSimpleSecurityBundle\Config\RoleManagementConfig;
use Psr\Container\ContainerInterface;

/**
 * Class ServiceProvider
 */
class ServiceProvider implements ContainerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $id The firewall name
     *
     * @return RoleManagementConfig
     */
    public function get($id)
    {
        if ($this->container->has($id)) {
            return $this->container->get($id);
        }

        throw new \InvalidArgumentException('Could not find RoleManagementConfig with firewall name "' . $id . '"');
    }

    /**
     * @param string $id The firewall name
     *
     * @return bool
     */
    public function has($id)
    {
        return $this->container->has($id);
    }
}
