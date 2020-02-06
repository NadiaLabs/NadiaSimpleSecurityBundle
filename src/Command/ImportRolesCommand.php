<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Config\RoleManagementConfig;
use Nadia\Bundle\NadiaSimpleSecurityBundle\DependencyInjection\Container\ServiceProvider;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Model\Role;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * A console command to import roles into database
 */
class ImportRolesCommand extends Command
{
    /**
     * @var string The default command name
     */
    protected static $defaultName = 'nadia:simple-security:import-roles';

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var ServiceProvider
     */
    private $roleManagementConfigServiceProvider;

    /**
     * @var ParameterBag
     */
    private $parameterBag;

    /**
     * ImportRolesCommand constructor.
     *
     * @param Registry        $doctrine
     * @param ServiceProvider $roleManagementConfigServiceProvider
     * @param ParameterBag    $parameterBag
     */
    public function __construct(
        Registry $doctrine,
        ServiceProvider $roleManagementConfigServiceProvider,
        ParameterBag $parameterBag
    ) {
        parent::__construct();

        $this->doctrine = $doctrine;
        $this->roleManagementConfigServiceProvider = $roleManagementConfigServiceProvider;
        $this->parameterBag = $parameterBag;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Import roles into database (MySQL or other RDBMS)')
            ->addArgument('firewall-name', InputArgument::REQUIRED, 'The firewall name to import roles')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $firewallName = $input->getArgument('firewall-name');
        /** @var RoleManagementConfig $roleManagementConfig */
        $roleManagementConfig = $this->roleManagementConfigServiceProvider->get($firewallName);
        $roleClassName = $roleManagementConfig->getRoleClassName();

        $om = $this->doctrine->getManager($roleManagementConfig->getObjectManagerName());
        $repo = $om->getRepository($roleClassName);
        $ref = new \ReflectionClass($roleClassName);
        $newRoles = [];

        foreach ($this->getRoles($roleManagementConfig) as $roleName) {
            $role = $repo->findOneBy(['role' => $roleName]);

            if (!$role instanceof Role) {
                $role = $ref->newInstance($roleName);
                $newRoles[] = $roleName;

                $om->persist($role);
            }
        }

        $om->flush();

        return 0;
    }

    /**
     * @param RoleManagementConfig $roleManagementConfig
     *
     * @return array
     */
    private function getRoles(RoleManagementConfig $roleManagementConfig)
    {
        $roles = [];

        foreach ($roleManagementConfig->getRoleGroups() as $roleGroup) {
            foreach ($roleGroup['roles'] as $role) {
                $roles[$role['role']] = $role['role'];
            }
        }

        return $roles;
    }
}
