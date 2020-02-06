<?php

$container->loadFromExtension('nadia_simple_security', [
    'role_managements' => require __DIR__ . '/test-role-managements.php',
]);
