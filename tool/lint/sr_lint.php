#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * SmartResponsor Canon Linter (G9).
 * Checks:
 *  - EN-only comments (basic Cyrillic detector)
 *  - singular class/interface/trait names (no trailing 's')
 *  - layer-first mirrors: Service ↔ ServiceInterface, Infra ↔ InfraInterface
 *  - file naming: single hyphen in filenames allowed, but not double '--' (we flag that in repo paths)
 */
$root = realpath(__DIR__ . '/../src/') ?: '.';
$src = $root . '/src';

$viol = [];
$allPhp = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS));
foreach ($allPhp as $f) {
    /** @var SplFileInfo $f */
    if (strtolower($f->getExtension()) !== 'php') continue;
    $path = $f->getPathname();
    $rel = substr($path, strlen($root) + 1);
    $txt = file_get_contents($path) ?: '';
    // 1) Cyrillic detection
    if (preg_match('/[\x{0400}-\x{04FF}]/u', $txt)) {
        $viol[] = ['type' => 'non-en-comment', 'file' => $rel, 'hint' => 'Cyrillic detected; comments must be EN-only'];
    }
    // 2) plural-ish class names
    if (preg_match_all('/\b(class|interface|trait)\s+([A-Z][A-Za-z0-9_]*)\b/', $txt, $m, PREG_SET_ORDER)) {
        foreach ($m as $row) {
            $name = $row[2];
            if (str_ends_with(strtolower($name), 's')) {
                $viol[] = ['type' => 'plural-name', 'file' => $rel, 'symbol' => $name, 'hint' => 'Class/Interface/Trait name should be singular'];
            }
        }
    }
    // 3) mirrors
    $mirror = null;
    if (str_contains($rel, 'src/Service/')) {
        $mirror = str_replace('/src/Service/', '/src/ServiceInterface/', $rel);
    } elseif (str_contains($rel, 'src/Infra/')) {
        $mirror = str_replace('/src/Infra/', '/src/InfraInterface/', $rel);
    }
    if ($mirror) {
        $mirrorPath = $root . '/' . $mirror;
        if (!is_file($mirrorPath)) {
            $viol[] = ['type' => 'missing-mirror', 'file' => $rel, 'mirror' => $mirror, 'hint' => 'Implementation must have Interface mirror'];
        }
    }
    // 4) filename rule: forbid double '--' in any path segment
    if (str_contains($rel, '--')) {
        $viol[] = ['type' => 'double-hyphen', 'file' => $rel, 'hint' => 'Use single hyphen only'];
    }
}

$reportDir = $root . '/report';
@mkdir($reportDir, 0775, true);

$out = [
    'ts' => date('c'),
    'violations' => $viol,
    'counts' => [
        'total' => count($viol),
        'byType' => array_count_values(array_map(fn($v) => $v['type'], $viol)),
    ],
];
file_put_contents($reportDir . '/lint_g9.json', json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "report/lint_g9.json written\n";
