<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * Class PhpNadiaSimpleSecurityExtensionTest
 */
class PhpNadiaSimpleSecurityExtensionTest extends NadiaSimpleSecurityExtensionTest
{
    protected function loadConfigFile(ContainerBuilder $container, $filename)
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Fixtures/config'));
        $loader->load($filename . '.php');
    }
}
