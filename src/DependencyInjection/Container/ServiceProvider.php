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
     * @var string
     */
    private $serviceIdPrefix;

    /**
     * @param ContainerInterface $container
     * @param string             $serviceIdPrefix
     */
    public function __construct(ContainerInterface $container, string $serviceIdPrefix)
    {
        $this->container = $container;
        $this->serviceIdPrefix = $serviceIdPrefix;
    }

    /**
     * @param string $id The firewall name
     *
     * @return RoleManagementConfig
     */
    public function get($id)
    {
        $id = $this->getServiceId($id);

        if ($this->container->has($id)) {
            return $this->container->get($id);
        }

        throw new \InvalidArgumentException('Could not find service "' . $id . '"');
    }

    /**
     * @param string $id The firewall name
     *
     * @return bool
     */
    public function has($id)
    {
        $id = $this->getServiceId($id);

        return $this->container->has($id);
    }

    /**
     * @param string $firewallName
     *
     * @return string
     */
    private function getServiceId($firewallName)
    {
        return $this->serviceIdPrefix . $firewallName;
    }
}
