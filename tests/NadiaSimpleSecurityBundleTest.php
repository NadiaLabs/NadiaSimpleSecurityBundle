<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Tests;

use Nadia\Bundle\NadiaSimpleSecurityBundle\DependencyInjection\Compiler\DoctrineOrmMappingPass;
use Nadia\Bundle\NadiaSimpleSecurityBundle\NadiaSimpleSecurityBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class NadiaSimpleSecurityBundleTest
 */
class NadiaSimpleSecurityBundleTest extends TestCase
{
    public function testBuild()
    {
        $bundle = new NadiaSimpleSecurityBundle();
        $container = new ContainerBuilder();

        $bundle->build($container);

        $count = 0;

        foreach ($container->getCompiler()->getPassConfig()->getBeforeOptimizationPasses() as $pass) {
            if ($pass instanceof DoctrineOrmMappingPass) {
                ++$count;
            }
        }


        $this->assertEquals(1, $count);
    }
}
