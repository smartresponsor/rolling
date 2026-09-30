<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$adminFiles = [
    'src/Controller/Admin/RollingDashboardController.php',
    'src/Controller/Admin/RollingRoleCrudController.php',
    'src/Controller/Admin/RollingRolePermissionCrudController.php',
    'src/Controller/Admin/RollingSubjectRoleAssignmentCrudController.php',
    'src/Controller/Admin/RollingAclRuleCrudController.php',
    'src/Controller/Admin/RollingAclMutationExecutionEventCrudController.php',
    'config/routes/rolling_admin_easyadmin.yaml',
];

$adminSurface = [];
foreach ($adminFiles as $relativePath) {
    if (is_file($root.'/'.$relativePath)) {
        $adminSurface[] = [
            'file' => $relativePath,
            'classification' => 'canon021_easyadmin_exception',
            'reason' => 'Native EasyAdmin administrative/back-office CRUD is permitted by Canon021 and remains separate from generic Cruding delivery.',
        ];
    }
}

$composer = json_decode((string) file_get_contents($root.'/composer.json'), true);
$dependencies = [
    'easyadmin_present' => is_array($composer) && isset($composer['require']['easycorp/easyadmin-bundle']),
    'cruding_present' => is_array($composer) && isset($composer['require']['cruding/crud']),
];

$routes = (string) @file_get_contents($root.'/config/routes/rolling_admin_easyadmin.yaml');
$adminRouteConfigured = '' !== $routes
    && str_contains($routes, 'src/Controller/Admin')
    && str_contains($routes, 'type: attribute');

$payload = [
    'status' => $dependencies['easyadmin_present'] && $adminRouteConfigured ? 'allowed' : 'attention',
    'surface_rule' => 'Canon021 keeps generic application CRUD in Cruding while explicitly permitting native EasyAdmin administrative/back-office CRUD.',
    'target_state' => 'zero component-local generic application CRUD duplication; native EasyAdmin back-office surface may remain',
    'dependencies' => $dependencies,
    'admin_route_configured' => $adminRouteConfigured,
    'administrative_surface' => $adminSurface,
    'summary' => [
        'administrative_surface_count' => count($adminSurface),
        'canon021_exception_applies' => true,
    ],
];

fwrite(STDOUT, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);
exit(0);
