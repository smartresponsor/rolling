<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$requiredFiles = [
    'src/Controller/Admin/RollingDashboardController.php',
    'src/Controller/Admin/RollingRoleCrudController.php',
    'src/Controller/Admin/RollingPermissionCrudController.php',
    'src/Controller/Admin/RollingRolePermissionCrudController.php',
    'src/Controller/Admin/RollingSubjectRoleAssignmentCrudController.php',
    'src/Controller/Admin/RollingAclRuleCrudController.php',
    'src/Controller/Admin/RollingAclMutationExecutionEventCrudController.php',
    'config/routes/rolling_admin_easyadmin.yaml',
];

$violations = [];
foreach ($requiredFiles as $relativePath) {
    if (!is_file($root.'/'.$relativePath)) {
        $violations[] = [
            'file' => $relativePath,
            'reason' => 'Required Rolling EasyAdmin admin surface file is missing.',
        ];
    }
}

$composer = json_decode((string) file_get_contents($root.'/composer.json'), true);
if (!is_array($composer) || !isset($composer['require']['easycorp/easyadmin-bundle'])) {
    $violations[] = [
        'file' => 'composer.json',
        'reason' => 'Rolling EasyAdmin admin surface requires easycorp/easyadmin-bundle.',
    ];
}

$routes = (string) @file_get_contents($root.'/config/routes/rolling_admin_easyadmin.yaml');
if (!str_contains($routes, 'src/Controller/Admin') || !str_contains($routes, 'type: attribute')) {
    $violations[] = [
        'file' => 'config/routes/rolling_admin_easyadmin.yaml',
        'reason' => 'Rolling EasyAdmin admin route import must load src/Controller/Admin as attributes.',
    ];
}

$payload = [
    'surface_rule' => 'EasyAdmin CRUD controllers are allowed only as admin surface under src/Controller/Admin.',
    'required_files' => $requiredFiles,
    'violations' => $violations,
];

fwrite(STDOUT, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);

if ([] !== $violations) {
    exit(1);
}
