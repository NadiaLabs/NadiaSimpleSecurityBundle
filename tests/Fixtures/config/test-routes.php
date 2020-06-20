<?php

use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\RoleEditable\User1;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\RoleEditable\User2;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\RoleEditable\User3;

return [
    [
        'target_class_name' => User1::class,
        'route_name' => 'user1',
    ],
    [
        'target_class_name' => User2::class,
        'route_name' => 'user2',
    ],
    [
        'target_class_name' => User3::class,
        'route_name' => 'user3',
    ],
];
