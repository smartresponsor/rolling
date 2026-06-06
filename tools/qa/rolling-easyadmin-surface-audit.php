<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

/** @return list<string> */
function rollingEaFiles(string $directory): array
{
    if (!is_dir($directory)) {
        return [];
    }

    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile() || 'php' !== $file->getExtension()) {
            continue;
        }

        $files[] = str_replace('\\', '/', $file->getPathname());
    }

    sort($files);

    return $files;
}

function rollingEaRelative(string $path, string $root): string
{
    $root = rtrim(str_replace('\\', '/', $root), '/').'/';

    return str_starts_with($path, $root) ? substr($path, strlen($root)) : $path;
}

$adminControllers = rollingEaFiles($root.'/src/Controller/Admin');
$violations = [];

foreach ($adminControllers as $file) {
    $relative = rollingEaRelative($file, $root);
    $content = (string) file_get_contents($file);
    $usesEasyAdminBase = str_contains($content, 'AbstractCrudController') || str_contains($content, 'AbstractDashboardController');
    if (!$usesEasyAdminBase) {
        $violations[] = [
            'file' => $relative,
            'reason' => 'Admin controller must be a native EasyAdmin dashboard or CRUD controller.',
        ];
    }
}

$composer = json_decode((string) file_get_contents($root.'/composer.json'), true);
$requiresEasyAdmin = is_array($composer) && isset($composer['require']['easycorp/easyadmin-bundle']);

$routes = (string) @file_get_contents($root.'/config/routes/rolling_admin_easyadmin.yaml');
$hasAdminRouteImport = str_contains($routes, 'src/Controller/Admin') && str_contains($routes, 'type: attribute');

$payload = [
    'surface_rule' => 'Rolling native EasyAdmin controllers are allowed only under src/Controller/Admin/*.',
    'admin_controller_count' => count($adminControllers),
    'admin_controllers' => array_map(static fn (string $file): string => rollingEaRelative($file, $root), $adminControllers),
    'requires_easyadmin_bundle' => $requiresEasyAdmin,
    'has_admin_attribute_route_import' => $hasAdminRouteImport,
    'violations' => $violations,
];

fwrite(STDOUT, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);

if (!$requiresEasyAdmin || !$hasAdminRouteImport || [] !== $violations) {
    exit(1);
}
