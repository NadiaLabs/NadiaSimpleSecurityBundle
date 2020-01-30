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

use Nadia\Bundle\NadiaSimpleSecurityBundle\DependencyInjection\NadiaSimpleSecurityExtension;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\Doctrine\Entity\Role;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class NadiaSimpleSecurityExtensionTest
 */
abstract class NadiaSimpleSecurityExtensionTest extends TestCase
{
    /**
     * @param ContainerBuilder $container
     * @param string           $filename  Filename without extension part (e.g. "test" for test.php/test.xml/test.yml)
     */
    abstract protected function loadConfigFile(ContainerBuilder $container, $filename);

    public function testParameters()
    {
        $container = $this->createContainerByConfigFile('test');

        $this->assertEquals(Role::class, $container->getParameter('nadia.simple_security.role_class'));
        $this->assertEquals(
            require __DIR__ . '/../Fixtures/config/test-role-managements.php',
            $container->getParameter('nadia.simple_security.role_managements')
        );
    }

    protected function createContainerByConfigFile($filename, array $data = [])
    {
        $container = $this->createBaseContainer($data);

        $container->registerExtension(new NadiaSimpleSecurityExtension());

        $this->loadConfigFile($container, $filename);

        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);

        $container->compile();

        return $container;
    }

    /**
     * @param array $data
     *
     * @return ContainerBuilder
     */
    protected function createBaseContainer(array $data = [])
    {
        // Make sure cache directory is different
        sleep(1);

        return new ContainerBuilder(new ParameterBag(array_merge([
            'kernel.bundles' => [
                'NadiaMenuBundle' => 'Nadia\\Bundle\\NadiaSimpleSecurityBundle\\NadiaSimpleSecurityBundle',
            ],
            'kernel.bundles_metadata' => [
                'NadiaSimpleSecurityBundle' => [
                    'namespace' => 'Nadia\\Bundle\\NadiaSimpleSecurityBundle',
                    'path' => __DIR__ . '/../..',
                ],
            ],
            'kernel.cache_dir' => sys_get_temp_dir() . '/nadia-simple-security-bundle-tests-' . time(),
            'kernel.project_dir' => __DIR__,
            'kernel.debug' => false,
            'kernel.environment' => 'test',
            'kernel.name' => 'kernel',
            'kernel.root_dir' => __DIR__,
            'kernel.container_class' => 'testContainer',
        ], $data)));
    }
}