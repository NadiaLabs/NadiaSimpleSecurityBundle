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
use Doctrine\Persistence\ObjectManager;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Config\RoleManagementConfig;
use Nadia\Bundle\NadiaSimpleSecurityBundle\DependencyInjection\Container\ServiceProvider;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Model\Role;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Model\RoleEditableInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AbstractRoleCommand
 */
abstract class AbstractRoleCommand extends Command
{
    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var ServiceProvider
     */
    protected $roleManagementConfigServiceProvider;

    /**
     * ImportRolesCommand constructor.
     *
     * @param Registry        $doctrine
     * @param ServiceProvider $roleManagementConfigServiceProvider
     */
    public function __construct(Registry $doctrine, ServiceProvider $roleManagementConfigServiceProvider)
    {
        parent::__construct();

        $this->doctrine = $doctrine;
        $this->roleManagementConfigServiceProvider = $roleManagementConfigServiceProvider;
    }

    /**
     * @param string $firewallName
     *
     * @return RoleManagementConfig
     */
    protected function getRoleManagementConfig(string $firewallName)
    {
        return $this->roleManagementConfigServiceProvider->get($firewallName);
    }

    /**
     * @param string               $username
     * @param RoleManagementConfig $config
     *
     * @return UserInterface|RoleEditableInterface
     */
    protected function getUser(string $username, RoleManagementConfig $config)
    {
        $user = $config->getUserProvider()->loadUserByUsername($username);

        if (empty($user)) {
            throw new \InvalidArgumentException('Username "' . $username . '" not found!');
        }

        if (!$user instanceof RoleEditableInterface) {
            throw new \InvalidArgumentException(
                'User should implement ' . RoleEditableInterface::class . ' interface.'
            );
        }

        return $user;
    }

    /**
     * @param string               $role
     * @param RoleManagementConfig $config
     *
     * @return string|Role|object|null
     */
    protected function getRole(string $role, RoleManagementConfig $config)
    {
        if (empty($config->getRoleClassName())) {
            return $role;
        }

        $return = $this->getObjectManager($config)
            ->getRepository($config->getRoleClassName())
            ->findOneBy(['role' => $role])
        ;

        if (empty($return)) {
            throw new \InvalidArgumentException('Role "' . $role . '" not found!');
        }
        if (!$return instanceof Role) {
            throw new \InvalidArgumentException('Role should extend ' . Role::class . ' class.');
        }

        return $return;
    }

    /**
     * @param RoleManagementConfig $config
     *
     * @return ObjectManager
     */
    protected function getObjectManager(RoleManagementConfig $config)
    {
        return $this->doctrine->getManager($config->getObjectManagerName());
    }

    /**
     * @param string          $messages
     * @param OutputInterface $output
     */
    protected function writeSuccessBlock(string $messages, OutputInterface $output)
    {
        /** @var FormatterHelper $formatHelper */
        $formatter = $this->getHelper('formatter');

        $output->writeln('');
        $output->writeln($formatter->formatBlock($messages, 'fg=black;bg=green', true));
        $output->writeln('');
    }

    /**
     * @param string          $messages
     * @param OutputInterface $output
     */
    protected function writeErrorBlock(string $messages, OutputInterface $output)
    {
        /** @var FormatterHelper $formatHelper */
        $formatter = $this->getHelper('formatter');

        $output->writeln('');
        $output->writeln($formatter->formatBlock($messages, 'error', true));
        $output->writeln('');
    }
}
