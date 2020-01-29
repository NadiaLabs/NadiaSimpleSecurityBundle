<?php

$container->loadFromExtension('nadia_simple_security', [
    'role_class' => 'Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Fixtures\Doctrine\Entity\Role',
    'role_managements' => require __DIR__ . '/test-role-managements.php',
]);
