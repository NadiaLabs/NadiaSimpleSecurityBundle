<?php

return [
    [
        'firewall_name' => 'main',
        'object_manager_name' => null,
        'user_provider' => 'test.user_provider',
        'role_class' => 'Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\Doctrine\Entity\Role',
        'role_groups' => [
            [
                'title' => 'Group title 1',
                'roles' => [
                    ['role' => 'ROLE_TEST_1_1', 'title' => 'Role title #1-1'],
                ],
            ],
            [
                'title' => 'Group title 2',
                'roles' => [
                    ['role' => 'ROLE_TEST_2_1', 'title' => 'Role title #2-1'],
                    ['role' => 'ROLE_TEST_2_2', 'title' => 'Role title #2-2'],
                ],
            ],
            [
                'title' => 'Group title 3',
                'roles' => [
                    ['role' => 'ROLE_TEST_3_1', 'title' => 'Role title #3-1'],
                    ['role' => 'ROLE_TEST_3_2', 'title' => 'Role title #3-2'],
                ],
            ],
            [
                'title' => 'Group title 4',
                'roles' => [
                    ['role' => 'ROLE_TEST_4_1', 'title' => 'Role title #4-1'],
                    ['role' => 'ROLE_TEST_4_2', 'title' => 'Role title #4-2'],
                ],
            ],
        ],
    ],
];
