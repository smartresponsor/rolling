<?php

declare(strict_types=1);

use App\Rolling\Service\Cruding\RollingCrudResourceDefinitionProvider;

require dirname(__DIR__, 2).'/vendor/autoload.php';

$root = dirname(__DIR__, 2);
$composer = json_decode((string) file_get_contents($root.'/composer.json'), true);
$provider = new RollingCrudResourceDefinitionProvider();
$definitions = array_map(
    static fn ($definition): array => $definition->toArray(),
    $provider->definitions(),
);

$legacyControllerCount = 0;
foreach ($definitions as $definition) {
    if (isset($definition['metadata']['legacy_controller'])) {
        ++$legacyControllerCount;
    }
}

$staleLegacyControllers = [];
$stalePermissionController = $root.'/src/Controller/Admin/RollingPermissionCrudController.php';
if (is_file($stalePermissionController)) {
    $staleLegacyControllers[] = [
        'file' => 'src/Controller/Admin/RollingPermissionCrudController.php',
        'reason' => 'Controller points to RolePermissionEntity but exposes componentName and description fields that are not present on that entity. Use rolling.role-permission metadata instead.',
    ];
}

$payload = [
    'status' => 'report',
    'dependencies' => [
        'cruding_required' => is_array($composer) && isset($composer['require']['cruding/crud']),
        'cruding_constraint' => is_array($composer) ? ($composer['require']['cruding/crud'] ?? null) : null,
    ],
    'resource_definition_count' => count($definitions),
    'legacy_controller_backed_definition_count' => $legacyControllerCount,
    'stale_legacy_controllers' => $staleLegacyControllers,
    'definitions' => $definitions,
    'next_steps' => [
        'add cruding/crud composer dependency with lock update',
        'translate RollingCrudResourceDefinition into Cruding provider registrations',
        'remove transitional EasyAdmin CRUD controllers after Cruding parity',
    ],
];

echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
exit(0);
