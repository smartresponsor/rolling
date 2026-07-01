<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$legacyFiles = [
    'src/Controller/Admin/RollingDashboardController.php',
    'src/Controller/Admin/RollingRoleCrudController.php',
    'src/Controller/Admin/RollingPermissionCrudController.php',
    'src/Controller/Admin/RollingRolePermissionCrudController.php',
    'src/Controller/Admin/RollingSubjectRoleAssignmentCrudController.php',
    'src/Controller/Admin/RollingAclRuleCrudController.php',
    'src/Controller/Admin/RollingAclMutationExecutionEventCrudController.php',
    'config/routes/rolling_admin_easyadmin.yaml',
];

$migrationCandidates = [];
foreach ($legacyFiles as $relativePath) {
    if (is_file($root.'/'.$relativePath)) {
        $migrationCandidates[] = [
            'file' => $relativePath,
            'target_owner' => 'cruding/crud',
            'reason' => 'Generic EasyAdmin CRUD/admin surface is transitional in Rolling and should migrate to Cruding.',
        ];
    }
}

$composer = json_decode((string) file_get_contents($root.'/composer.json'), true);
$dependencies = [
    'easyadmin_present' => is_array($composer) && isset($composer['require']['easycorp/easyadmin-bundle']),
    'cruding_present' => is_array($composer) && isset($composer['require']['cruding/crud']),
];

$findings = [];
if ($dependencies['easyadmin_present']) {
    $findings[] = [
        'file' => 'composer.json',
        'classification' => 'legacy_dependency_candidate',
        'reason' => 'easycorp/easyadmin-bundle remains required while Rolling owns transitional EasyAdmin CRUD controllers.',
    ];
}

$routes = (string) @file_get_contents($root.'/config/routes/rolling_admin_easyadmin.yaml');
if ('' !== $routes && str_contains($routes, 'src/Controller/Admin') && str_contains($routes, 'type: attribute')) {
    $findings[] = [
        'file' => 'config/routes/rolling_admin_easyadmin.yaml',
        'classification' => 'legacy_route_import_candidate',
        'reason' => 'Rolling imports EasyAdmin admin controllers directly; target state routes generic CRUD through Cruding.',
    ];
}

$payload = [
    'status' => 'report',
    'surface_rule' => 'Rolling must not treat EasyAdmin CRUD controllers as canonical once Cruding owns generic CRUD.',
    'target_state' => 'zero generic CRUD controllers and zero generic CRUD routes in Rolling',
    'dependencies' => $dependencies,
    'migration_candidates' => $migrationCandidates,
    'findings' => $findings,
    'summary' => [
        'migration_candidate_count' => count($migrationCandidates),
        'finding_count' => count($findings),
    ],
];

fwrite(STDOUT, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);
exit(0);
