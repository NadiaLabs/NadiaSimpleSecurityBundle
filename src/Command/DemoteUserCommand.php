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

use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A console command to remove a user's role
 */
class DemoteUserCommand extends AbstractRoleCommand
{
    /**
     * @var string The default command name
     */
    protected static $defaultName = 'nadia:simple-security:demote-user';

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Remove a user\'s role')
            ->addArgument('firewall-name', InputArgument::REQUIRED, 'The firewall name')
            ->addArgument('username', InputArgument::REQUIRED, 'The username')
            ->addArgument('role', InputArgument::REQUIRED, 'The role name')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getRoleManagementConfig($input->getArgument('firewall-name'));
        $user = $this->getUser($input->getArgument('username'), $config);
        $role = $this->getRole($input->getArgument('role'), $config);

        if (!$user->hasRole($role)) {
            $this->writeErrorBlock('User doesn\'t have this role "' . $role . '".', $output);
            return 1;
        }

        $user->removeRole($role);

        $om = $this->getObjectManager($config);
        $om->persist($user);
        $om->flush();

        $this->writeSuccessBlock('Removed user\'s role "' . $role . '" successfully.', $output);

        return 0;
    }
}
