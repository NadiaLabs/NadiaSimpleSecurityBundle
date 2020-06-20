<?php

$container->loadFromExtension('nadia_simple_security', [
    'super_admin_roles' => ['ROLE_SUPER_ADMIN', 'ROLE_VIP_SUPER_ADMIN'],
    'role_managements' => require __DIR__ . '/test-role-managements.php',
    'routes' => require __DIR__ . '/test-routes.php',
]);
