<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

/** @return list<string> */
function rolling81CollectFiles(string $root, array $extensions): array
{
    $files = [];
    $skip = [
        '/vendor/',
        '/var/cache/',
        '/var/log/',
        '/node_modules/',
        '/.git/',
        '/tools/qa/rolling-symfony81-audit.php',
    ];

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }

        $path = str_replace('\\', '/', $file->getPathname());
        foreach ($skip as $fragment) {
            if (str_contains($path, $fragment)) {
                continue 2;
            }
        }

        $extension = strtolower((string) $file->getExtension());
        if (in_array($extension, $extensions, true)) {
            $files[] = $path;
        }
    }

    sort($files);

    return $files;
}

function rolling81Relative(string $path, string $root): string
{
    $root = rtrim(str_replace('\\', '/', $root), '/').'/';

    return str_starts_with($path, $root) ? substr($path, strlen($root)) : $path;
}

/** @return list<array{file:string,line:int,match:string,severity:string,note:string}> */
function rolling81ScanPattern(array $files, string $root, string $pattern, string $severity, string $note): array
{
    $hits = [];
    foreach ($files as $file) {
        $lines = @file($file, FILE_IGNORE_NEW_LINES);
        if (false === $lines) {
            continue;
        }

        foreach ($lines as $index => $line) {
            if (preg_match($pattern, $line) === 1) {
                $hits[] = [
                    'file' => rolling81Relative($file, $root),
                    'line' => $index + 1,
                    'match' => trim($line),
                    'severity' => $severity,
                    'note' => $note,
                ];
            }
        }
    }

    return $hits;
}

$phpFiles = rolling81CollectFiles($root, ['php']);
$textFiles = rolling81CollectFiles($root, ['php', 'yaml', 'yml', 'xml', 'neon', 'md', 'adoc', 'json', 'sh', 'ps1']);
$testPhpFiles = array_values(array_filter($phpFiles, static fn (string $file): bool => str_contains($file, '/tests/')));
$commandPhpFiles = array_values(array_filter($phpFiles, static fn (string $file): bool => str_contains($file, '/Command/') || str_contains($file, '/Console/Command/')));
$messengerTextFiles = array_values(array_filter($textFiles, static fn (string $file): bool => !str_ends_with($file, '/config/reference.php')));

$checks = [
    'deprecated_httpkernel_moved_classes' => rolling81ScanPattern(
        $phpFiles,
        $root,
        '/Symfony\\\\Component\\\\HttpKernel\\\\(Bundle\\\\BundleInterface|DependencyInjection\\\\MergeExtensionConfigurationPass|Config\\\\FileLocator)\\b/',
        'blocker',
        'Symfony 8.1 keeps these as BC aliases, but they are deprecated; use DependencyInjection namespaces.'
    ),
    'implicit_target_candidates' => rolling81ScanPattern(
        $phpFiles,
        $root,
        '/function\s+__construct\s*\(|private\s+readonly\s+[^$\n]+\$[a-zA-Z0-9_]*(logger|cache|client|provider|store|repository|resolver|registry|bus|connection|entityManager)[a-zA-Z0-9_]*\b/i',
        'review',
        'Constructor/service argument may rely on implicit named aliasing; use #[Target(...)] when multiple implementations exist.'
    ),
    'tagged_iterator_magic_priority' => rolling81ScanPattern(
        $phpFiles,
        $root,
        '/default(Index|Priority)Method|getDefault[A-Za-z0-9_]*Name\s*\(|getDefaultPriority\s*\(/',
        'blocker',
        'Symfony 8.1 deprecates magic default index/priority methods; use #[AsTaggedItem].'
    ),
    'manual_request_payload_mapping' => rolling81ScanPattern(
        $phpFiles,
        $root,
        '/json_decode\s*\(\s*\(?\s*string\)?\s*\$[a-zA-Z0-9_]+->getContent\s*\(|->request->all\s*\(|->files->all\s*\(/',
        'review',
        'Candidate for DTO-first request mapping and Symfony 8.1 #[MapRequestPayload] improvements.'
    ),
    'uploaded_file_endpoint_candidates' => rolling81ScanPattern(
        $phpFiles,
        $root,
        '/UploadedFile|->files->|multipart\/form-data|move\s*\(/',
        'review',
        'Check whether Symfony 8.1 uploaded-file DTO mapping can replace manual merge/hydration.'
    ),
    'httpclient_cache_ttl_candidates' => rolling81ScanPattern(
        $textFiles,
        $root,
        '/CachingHttpClient|HttpClient|CacheInterface|Psr16|SimpleCache|ttl|TTL|max_ttl|maxTtl|null\s*\)/i',
        'review',
        'Audit cache TTL assumptions; Symfony 8.1 defaults CachingHttpClient maxTtl to 86400s.'
    ),
    'console_execution_result_candidates' => rolling81ScanPattern(
        array_values(array_unique(array_merge($testPhpFiles, $commandPhpFiles))),
        $root,
        '/CommandTester|ApplicationTester|getDisplay\s*\(|capture_stderr_separately|protected\s+function\s+execute\s*\(/',
        'review',
        'Console tests with stdout/stderr/status assertions may benefit from Symfony 8.1 ExecutionResult.'
    ),
    'messenger_worker_candidates' => rolling81ScanPattern(
        $messengerTextFiles,
        $root,
        '/messenger:consume|--no-reset|--fetch-size|failure_transport|failed_message|retry_strategy|AmqpPriorityStamp|redis_cluster/i',
        'review',
        'Check worker docs/config for Symfony 8.1 fetch-size, no-reset=N, priority, and failure pipeline behavior.'
    ),
    'checkbox_form_candidates' => rolling81ScanPattern(
        $phpFiles,
        $root,
        '/CheckboxType::class|expanded\s*=>\s*true|false_values|falseValues|is_required|required\s*=>\s*false/',
        'review',
        'Add regression tests when old null/not-submitted checkbox behavior affected business logic.'
    ),
    'http_coupling_in_core_candidates' => rolling81ScanPattern(
        array_values(array_filter($phpFiles, static fn (string $file): bool => !str_contains($file, '/src/Service/Http/') && !str_contains($file, '/src/Controller/Admin/'))),
        $root,
        '#Symfony\\\\Component\\\\HttpFoundation\\\\(?:Request|Response|JsonResponse)|Symfony\\\\Component\\\\HttpKernel\\\\|RequestStack|HttpKernelInterface|ControllerResolver|RouterInterface|function\s+[^()]+\([^)]*Request\s+\$#',
        'review',
        'Non-HTTP core should not depend on HTTP runtime unless it is an explicit edge service.'
    ),
];

$blockers = 0;
$reviews = 0;
foreach ($checks as $hits) {
    foreach ($hits as $hit) {
        if ('blocker' === $hit['severity']) {
            ++$blockers;
        } else {
            ++$reviews;
        }
    }
}

$payload = [
    'gate' => 'Rolling Symfony 8.1 readiness audit',
    'source_policy' => 'Audit only; does not require composer update and does not transform business code.',
    'summary' => [
        'php_files_scanned' => count($phpFiles),
        'text_files_scanned' => count($textFiles),
        'blocker_count' => $blockers,
        'review_count' => $reviews,
    ],
    'checks' => $checks,
    'exit_policy' => [
        'blocker_count_gt_zero' => 'exit 1',
        'review_count_only' => 'exit 0 with report candidates',
    ],
];

fwrite(STDOUT, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);

if ($blockers > 0) {
    exit(1);
}
