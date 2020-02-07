<?php

/**
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\DependencyInjection\Compiler;

use Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class DoctrineOrmMappingPass
 */
class DoctrineOrmMappingPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $objectManagerNames = $container->getParameter('nadia.simple_security.object_manager_names');

        $namespaces = [
            realpath(__DIR__ . '/Resources/config/doctrine-mapping') => 'Nadia\Bundle\NadiaSimpleSecurityBundle\Model',
        ];
        $driverIdPattern = 'doctrine.orm.%s_metadata_driver';
        $locatorDef = new Definition(SymfonyFileLocator::class, [$namespaces, '.orm.yml']);
        $mappingDriverDef = new Definition(YamlDriver::class, [$locatorDef]);

        foreach ($objectManagerNames as $objectManagerName) {
            $driverId = sprintf($driverIdPattern, $objectManagerName);
            $chainDriverDef = $container->getDefinition($driverId);

            foreach ($namespaces as $namespace) {
                $chainDriverDef->addMethodCall('addDriver', [$mappingDriverDef, $namespace]);
            }
        }
    }
}
