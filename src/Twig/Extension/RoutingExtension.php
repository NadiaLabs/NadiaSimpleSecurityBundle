<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Twig\Extension;

use Nadia\Bundle\NadiaSimpleSecurityBundle\Model\RoleEditableInterface;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Routing\Generator\EditRolesUrlGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class RoutingExtension
 */
class RoutingExtension extends AbstractExtension
{
    /**
     * @var EditRolesUrlGenerator
     */
    protected $urlGenerator;

    /**
     * RoutingExtension constructor.
     *
     * @param EditRolesUrlGenerator $urlGenerator
     */
    public function __construct(EditRolesUrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('nadia_simple_security_edit_roles_url', [$this, 'generateEditRolesUrl']),
        );
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
    public function generateEditRolesUrl($firewallName, RoleEditableInterface $entity, $pk)
    {
        return $this->urlGenerator->generate($firewallName, $entity, $pk);
    }
}
