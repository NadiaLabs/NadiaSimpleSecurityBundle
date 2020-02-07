<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Role\Role;

/**
 * Class SuperAdminRoleVoter
 */
class SuperAdminRoleVoter implements VoterInterface
{
    /**
     * @var string[]
     */
    private $validSuperAdminRoles;

    /**
     * SuperAdminRoleVoter constructor.
     *
     * @param string[] $validSuperAdminRoles
     *
     */
    public function __construct(array $validSuperAdminRoles)
    {
        $this->validSuperAdminRoles = $validSuperAdminRoles;
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        $result = VoterInterface::ACCESS_ABSTAIN;
        $roles = $this->extractRoles($token);

        $diff = array_intersect($this->validSuperAdminRoles, $roles);

        if (count($diff) > 0) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return $result;
    }

    /**
     * @param TokenInterface $token
     *
     * @return string[]
     */
    protected function extractRoles(TokenInterface $token)
    {
        if (method_exists($token, 'getRoleNames')) {
            return $token->getRoleNames();
        }

        return array_map(
            function (Role $role) {
                return $role->getRole();
            },
            $token->getRoles(false)
        );
    }
}
