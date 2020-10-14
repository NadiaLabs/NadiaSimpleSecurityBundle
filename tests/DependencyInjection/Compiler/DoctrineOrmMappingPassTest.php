<?php

/**
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\DependencyInjection\Compiler;

use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Nadia\Bundle\NadiaSimpleSecurityBundle\DependencyInjection\Compiler\DoctrineOrmMappingPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class DoctrineOrmMappingPass
 */
class DoctrineOrmMappingPassTest extends TestCase
{
    public function testProcess()
    {
        /** @var Definition[] $mappingDriverChainDefs */
        $mappingDriverChainDefs = [
            'default' => new Definition(MappingDriverChain::class),
            'test' => new Definition(MappingDriverChain::class),
        ];

        $container = new ContainerBuilder();

        $container->setParameter('nadia.simple_security.object_manager_names', array_keys($mappingDriverChainDefs));
        $container->setDefinition('doctrine.orm.default_metadata_driver', $mappingDriverChainDefs['default']);
        $container->setDefinition('doctrine.orm.test_metadata_driver', $mappingDriverChainDefs['test']);

        $pass = new DoctrineOrmMappingPass();

        $pass->process($container);

        foreach ($mappingDriverChainDefs as $key => $mappingDriverChainDef) {
            $calls = $mappingDriverChainDef->getMethodCalls();

            $this->assertEquals(1, count($calls));
            $this->assertEquals('addDriver', $calls[0][0]);
            $this->assertEquals(2, count($calls[0][1]));
            $this->assertEquals('Nadia\Bundle\NadiaSimpleSecurityBundle\Model', $calls[0][1][1]);
        }
    }

    public function testEmptyObjectManagerNames()
    {
        $container = $this->createMock(ContainerBuilder::class);

        $container->expects($this->once())->method('hasParameter')->willReturn(false);

        $container->expects($this->never())->method('getParameter');

        $pass = new DoctrineOrmMappingPass();

        $pass->process($container);
    }
}
