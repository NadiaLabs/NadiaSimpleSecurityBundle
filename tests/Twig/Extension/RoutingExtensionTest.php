<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Twig\Extension;

use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\RoleEditable\User1;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Twig\Extension\RoutingExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * Class RoutingExtensionTest
 */
class RoutingExtensionTest extends TestCase
{
    public function testGenerateEditRolesUrl()
    {
        $template = '{{- nadia_simple_security_edit_roles_url(firewallName, user, pk)|raw -}}';
        $viewData = [
            'firewallName' => 'main',
            'user' => new User1(),
            'pk' => 123456,
        ];
        $queryString = http_build_query(['class' => get_class($viewData['user']), 'pk' => $viewData['pk']]);
        $expectedUrl = '/nadia/simple-security/edit-user-roles/' .
            urlencode($viewData['firewallName']) . '?' . $queryString;

        $twig = $this->getTwig($expectedUrl);

        $template = $twig->createTemplate($template);
        $actualUrl = $template->render($viewData);

        $this->assertEquals($expectedUrl, $actualUrl);
    }

    /**
     * @param string $expectedUrl
     *
     * @return Environment
     */
    private function getTwig($expectedUrl)
    {
        $twig = new Environment(new ArrayLoader());
        $twig->addExtension(new RoutingExtension(new StubUrlGenerator($expectedUrl)));

        return $twig;
    }
}
