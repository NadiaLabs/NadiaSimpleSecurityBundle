<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class NadiaSimpleSecurityExtension
 */
class NadiaSimpleSecurityExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $locator = new FileLocator(__DIR__ . '/../../config/');
        $loader  = new YamlFileLoader($container, $locator);

        $loader->load('services.yml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('nadia.simple_security.role_class', $config['role_class']);
        $container->setParameter('nadia.simple_security.role_managements', $config['role_managements']);
    }

    /**
     * @inheritdoc
     */
    public function getNamespace()
    {
        return 'http://nadialabs.com.tw/schema/dic/simple-security';
    }
}
