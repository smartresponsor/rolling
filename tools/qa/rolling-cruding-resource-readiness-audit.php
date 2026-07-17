<?php

declare(strict_types=1);

use App\Rolling\Service\Cruding\RollingCrudResourceDefinitionProvider;

require dirname(__DIR__, 2).'/vendor/autoload.php';

$root = dirname(__DIR__, 2);
$composer = json_decode((string) file_get_contents($root.'/composer.json'), true);
$services = (string) file_get_contents($root.'/config/services.yaml');
$providerInterface = 'App\\Rolling\\ServiceInterface\\Cruding\\RollingCrudResourceDefinitionProviderInterface';
$providerService = 'App\\Rolling\\Service\\Cruding\\RollingCrudResourceDefinitionProvider';
$providerAliasConfigured = str_contains(
    $services,
    $providerInterface.": '@".$providerService."'",
);

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
$forbiddenDuplicateControllers = [
    'src/Controller/Admin/RollingPermissionCrudController.php' => [
        'class' => 'RollingPermissionCrudController',
        'classification' => 'deprecated_duplicate',
        'reason' => 'Role-permission CRUD is represented by RollingRolePermissionCrudController and rolling.role-permission metadata. The duplicate controller class must not return.',
    ],
];

foreach ($forbiddenDuplicateControllers as $relativePath => $finding) {
    $contents = (string) @file_get_contents($root.'/'.$relativePath);
    if ('' === $contents || !str_contains($contents, 'class '.$finding['class'])) {
        continue;
    }

    $staleLegacyControllers[] = [
        'file' => $relativePath,
        ...$finding,
    ];
}

$ready = [] === $staleLegacyControllers && $providerAliasConfigured;

$payload = [
    'status' => $ready ? 'ready' : 'blocked',
    'dependencies' => [
        'cruding_required' => is_array($composer) && isset($composer['require']['cruding/crud']),
        'cruding_constraint' => is_array($composer) ? ($composer['require']['cruding/crud'] ?? null) : null,
        'provider_alias_configured' => $providerAliasConfigured,
        'provider_interface' => $providerInterface,
        'provider_service' => $providerService,
    ],
    'resource_definition_count' => count($definitions),
    'legacy_controller_backed_definition_count' => $legacyControllerCount,
    'stale_legacy_controllers' => $staleLegacyControllers,
    'definitions' => $definitions,
    'next_steps' => [
        'translate RollingCrudResourceDefinition into Cruding provider registrations',
        'remove transitional EasyAdmin CRUD controllers after Cruding parity',
    ],
];

echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
exit($ready ? 0 : 1);
