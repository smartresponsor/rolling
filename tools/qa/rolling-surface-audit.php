<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

/** @return list<string> */
function collectFiles(string $directory, string $suffix): array
{
    if (!is_dir($directory)) {
        return [];
    }

    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));

    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }

        $path = str_replace('\\', '/', $file->getPathname());
        if (str_ends_with($path, $suffix)) {
            $files[] = $path;
        }
    }

    sort($files);

    return $files;
}

/** @return list<string> */
function relativePaths(array $paths, string $root): array
{
    $normalizedRoot = rtrim(str_replace('\\', '/', $root), '/').'/';

    return array_map(
        static fn (string $path): string => str_starts_with($path, $normalizedRoot) ? substr($path, strlen($normalizedRoot)) : $path,
        $paths,
    );
}

$controllerFiles = collectFiles($root.'/src/Controller', '.php');
$genericControllers = array_values(array_filter(
    relativePaths($controllerFiles, $root),
    static fn (string $path): bool => !str_starts_with($path, 'src/Controller/Admin/'),
));

$routeFiles = collectFiles($root.'/config/routes', '.yaml');
$routeFiles[] = $root.'/config/routes.yaml';
$routeFiles = array_values(array_unique($routeFiles));
sort($routeFiles);

$routesWithoutController = [];
foreach ($routeFiles as $routeFile) {
    if (!is_file($routeFile)) {
        continue;
    }

    $content = file_get_contents($routeFile);
    if (false === $content) {
        continue;
    }

    $hasPath = preg_match('/^\s*path\s*:/m', $content) === 1;
    $hasController = preg_match('/^\s*(controller|_controller)\s*:/m', $content) === 1;
    $isImportOnly = !$hasPath && preg_match('/^\s*resource\s*:/m', $content) === 1;

    if ($hasPath && !$hasController && !$isImportOnly) {
        $routesWithoutController[] = str_replace('\\', '/', substr($routeFile, strlen($root) + 1));
    }
}

$ignoredRuntimePaths = array_keys(array_filter([
    'var/cache' => is_dir($root.'/var/cache'),
    'var/phpstan' => is_dir($root.'/var/phpstan'),
    'var/.php-cs-fixer.cache' => is_file($root.'/var/.php-cs-fixer.cache'),
], static fn (bool $exists): bool => $exists));

$payload = [
    'surface_rule' => 'Rolling front/public surface is zero-controller. EasyAdmin controllers are allowed only under src/Controller/Admin.',
    'generic_controller_count' => count($genericControllers),
    'generic_controllers' => $genericControllers,
    'route_files_without_controller_count' => count($routesWithoutController),
    'route_files_without_controller' => $routesWithoutController,
    'ignored_runtime_paths_present' => $ignoredRuntimePaths,
    'ignored_runtime_paths_policy' => 'Informational only: ignored local caches are not part of the package surface and do not fail this boundary audit.',
];

fwrite(STDOUT, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);

if ([] !== $genericControllers || [] !== $routesWithoutController) {
    exit(1);
}
