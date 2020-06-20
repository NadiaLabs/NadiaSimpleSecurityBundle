<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Routing\Generator;

use Nadia\Bundle\NadiaSimpleSecurityBundle\Model\RoleEditableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Generate URL for editing target entity's roles
 */
class EditRolesUrlGenerator
{
    public const DEFAULT_ROUTE_NAME = '_nadia_simple_security_edit_roles';

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var array
     */
    protected $routeMap;

    /**
     * AssetExtension constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     * @param array $routeMap
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, array $routeMap = [])
    {
        $this->urlGenerator = $urlGenerator;
        $this->routeMap = $routeMap;
    }

    /**
     * Generate URL for editing target entity's roles
     *
     * @param string                $firewallName
     * @param RoleEditableInterface $entity       An entity instance that implements RoleEditableInterface
     * @param int|string|array      $pk
     *
     * @return string
     */
    public function generate($firewallName, RoleEditableInterface $entity, $pk)
    {
        $parameters = [
            'firewallName' => $firewallName,
            'class' => get_class($entity),
            'pk' => $pk,
        ];
        $routeName = self::DEFAULT_ROUTE_NAME;

        if (!empty($this->routeMap[$parameters['class']])) {
            $routeName = $this->routeMap[$parameters['class']];
        }

        return $this->urlGenerator->generate($routeName, $parameters);
    }
}
