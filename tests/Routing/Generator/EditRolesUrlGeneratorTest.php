<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Routing\Generator;

use Nadia\Bundle\NadiaSimpleSecurityBundle\Routing\Generator\EditRolesUrlGenerator;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\RoleEditable\User1;
use PHPUnit\Framework\TestCase;

/**
 * Class EditRolesUrlGeneratorTest
 */
class EditRolesUrlGeneratorTest extends TestCase
{
    public function testGenerateEditRolesUrl()
    {
        $firewallName = 'main';
        $entity = new User1();
        $pk = 123456;

        $expectedUrls = [
            EditRolesUrlGenerator::DEFAULT_ROUTE_NAME => '/nadia/simple-security/edit-user-roles/' .
                urlencode($firewallName) . '?' . http_build_query(['class' => get_class($entity), 'pk' => $pk]),
            'user1' => '/nadia/simple-security/edit-user-roles/user1/' .
                urlencode($firewallName) . '?' . http_build_query(['class' => get_class($entity), 'pk' => $pk]),
        ];

        $urlGenerator = $this->createEditRolesUrlGenerator($expectedUrls);
        $expectedUrl = $expectedUrls[EditRolesUrlGenerator::DEFAULT_ROUTE_NAME];
        $actualUrl = $urlGenerator->generate($firewallName, $entity, $pk);

        $this->assertEquals($expectedUrl, $actualUrl);

        $routeMap = [
            get_class($entity) => 'user1',
        ];
        $urlGenerator = $this->createEditRolesUrlGenerator($expectedUrls, $routeMap);
        $expectedUrl = $expectedUrls['user1'];
        $actualUrl = $urlGenerator->generate($firewallName, $entity, $pk);

        $this->assertEquals($expectedUrl, $actualUrl);
    }

    /**
     * @param array $expectedUrls
     * @param array $routeMap
     *
     * @return EditRolesUrlGenerator
     */
    private function createEditRolesUrlGenerator(array $expectedUrls, array $routeMap = [])
    {
        $stubUrlGenerator = new StubUrlGenerator($expectedUrls);

        return new EditRolesUrlGenerator($stubUrlGenerator, $routeMap);
    }
}
