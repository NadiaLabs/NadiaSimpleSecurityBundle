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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class RoutingExtension
 */
class RoutingExtension extends AbstractExtension
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * AssetExtension constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
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
     * Render javascript with PHP data
     *
     * @param string                $firewallName
     * @param RoleEditableInterface $entity       An entity instance that implements RoleEditableInterface
     * @param int|string|array      $pk
     *
     * @return string
     */
    public function generateEditRolesUrl($firewallName, RoleEditableInterface $entity, $pk)
    {
        $parameters = [
            'firewallName' => $firewallName,
            'class' => get_class($entity),
            'pk' => $pk,
        ];

        return $this->urlGenerator->generate('_nadia_simple_security_edit_roles', $parameters);
    }
}
