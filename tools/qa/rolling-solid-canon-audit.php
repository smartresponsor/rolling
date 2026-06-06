<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

$patterns = [
    'generic_controllers' => 'src/Controller/**/*.php',
    'obsolete_infrastructure_contracts' => [
        'src/Infrastructure/Audit/AuditWriter.php',
        'src/Infrastructure/Cache/KeyValueCache.php',
        'src/Infrastructure/Acl/Source/GithubSubjectResolver.php',
    ],
    'adapter_suffix_files' => 'src/**/*Adapter.php',
];

$genericControllers = [];
foreach (glob($root.'/src/Controller/**/*.php') ?: [] as $file) {
    $relative = str_replace($root.'/', '', $file);
    if (!str_starts_with($relative, 'src/Controller/Admin/')) {
        $genericControllers[] = $relative;
    }
}

$obsolete = [];
foreach ($patterns['obsolete_infrastructure_contracts'] as $relative) {
    if (is_file($root.'/'.$relative)) {
        $obsolete[] = $relative;
    }
}

$adapterSuffix = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/src', FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $file) {
    if (!$file->isFile() || 'php' !== $file->getExtension()) {
        continue;
    }
    $relative = str_replace($root.'/', '', $file->getPathname());
    if (str_ends_with($relative, 'Adapter.php')) {
        $adapterSuffix[] = $relative;
    }
}

$contents = [];
foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/src', FilesystemIterator::SKIP_DOTS)) as $file) {
    if (!$file->isFile() || 'php' !== $file->getExtension()) {
        continue;
    }
    $relative = str_replace($root.'/', '', $file->getPathname());
    $text = file_get_contents($file->getPathname()) ?: '';
    foreach ([
        'Infrastructure\\Cache\\KeyValueCache',
        'Infrastructure\\Cache\\Psr16CacheAdapter',
        'Infrastructure\\Acl\\Source\\GithubSubjectResolver',
        'DefaultGithubResolver',
        'Infrastructure\\Audit\\AuditWriter;',
    ] as $needle) {
        if (str_contains($text, $needle)) {
            $contents[] = $relative.' contains '.$needle;
        }
    }
}

$result = [
    'generic_controller_count' => count($genericControllers),
    'generic_controllers' => $genericControllers,
    'obsolete_infrastructure_contract_count' => count($obsolete),
    'obsolete_infrastructure_contracts' => $obsolete,
    'adapter_suffix_file_count' => count($adapterSuffix),
    'adapter_suffix_files' => $adapterSuffix,
    'obsolete_reference_count' => count($contents),
    'obsolete_references' => $contents,
];

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;

if ($genericControllers || $obsolete || $adapterSuffix || $contents) {
    exit(1);
}
