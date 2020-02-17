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

use Nadia\Bundle\NadiaSimpleSecurityBundle\Config\RoleManagementConfig;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Model\Role;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A console command to import roles into database
 */
class ImportRolesCommand extends AbstractRoleCommand
{
    /**
     * @var string The default command name
     */
    protected static $defaultName = 'nadia:simple-security:import-roles';

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Import roles into database (MySQL or other RDBMS)')
            ->addArgument('firewall-name', InputArgument::REQUIRED, 'The firewall name to import roles')
        ;
    }

    /**
     * @inheritdoc
     *
     * @throws \ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getRoleManagementConfig($input->getArgument('firewall-name'));
        $om = $this->getObjectManager($config);
        $newRoles = $this->getNewRoles($config);

        if (!empty($newRoles)) {
            foreach ($newRoles as $role) {
                $om->persist($role);
            }

            $om->flush();

            $output->writeln("\n" . 'Added new roles:' . "\n");

            foreach ($newRoles as $role) {
                $output->writeln('  - ' . $role->getRole());
            }

            $output->writeln('');
        } else {
            $output->writeln("\n" . 'There is nothing to update.' . "\n");
        }

        return 0;
    }

    /**
     * @param RoleManagementConfig $config
     *
     * @return Role[]
     *
     * @throws \ReflectionException
     */
    protected function getNewRoles(RoleManagementConfig $config)
    {
        $roleClassName = $config->getRoleClassName();
        $repo = $this->getObjectManager($config)->getRepository($roleClassName);
        $ref = new \ReflectionClass($roleClassName);
        $newRoles = [];

        foreach ($config->getRoleGroups() as $roleGroup) {
            foreach ($roleGroup['roles'] as $role) {
                $roleName = $role['role'];

                if (!$repo->findOneBy(['role' => $roleName]) instanceof Role) {
                    $newRoles[] = $ref->newInstance($roleName);
                }
            }
        }

        return $newRoles;
    }
}
