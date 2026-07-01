<?php

declare(strict_types=1);

final class RollingObjectingAdoptionAudit
{
    private const SYSTEM_FIELD_PACKS = [
        'createdAt' => 'audit',
        'createdBy' => 'audit',
        'modifiedAt' => 'audit',
        'modifiedBy' => 'audit',
        'deleted' => 'soft_delete',
        'deletedAt' => 'soft_delete',
        'deletedBy' => 'soft_delete',
        'enabled' => 'state',
        'active' => 'state',
        'status' => 'state',
        'version' => 'version',
        'etag' => 'version',
        'uuid' => 'identity',
        'slug' => 'identity',
    ];

    private const BUSINESS_EXCEPTIONS = [
        'src/Entity/Role/RoleAclMutationExecutionEventEntity.php' => [
            'createdAt' => 'domain_event_timestamp',
            'status' => 'domain_execution_status',
            'succeeded' => 'domain_execution_result',
        ],
    ];

    private string $root;

    public function __construct(string $root)
    {
        $this->root = rtrim($root, DIRECTORY_SEPARATOR);
    }

    /** @return array<string, mixed> */
    public function run(): array
    {
        $composer = $this->readComposer();
        $objectingReferences = $this->findObjectingReferences();
        $findings = [];

        foreach ($this->collectPhpFiles($this->root.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Entity') as $file) {
            $relative = $this->relativePath($file);
            foreach ($this->extractPrivateFields((string) file_get_contents($file)) as $field) {
                $pack = self::SYSTEM_FIELD_PACKS[$field] ?? null;
                if (null === $pack) {
                    continue;
                }

                $exception = self::BUSINESS_EXCEPTIONS[$relative][$field] ?? null;
                $findings[] = [
                    'file' => $relative,
                    'field' => $field,
                    'objecting_pack' => $pack,
                    'classification' => null === $exception ? 'system_field_candidate' : 'business_exception_candidate',
                    'exception_reason' => $exception,
                ];
            }
        }

        return [
            'status' => 'report',
            'dependency' => [
                'objecting_object_required' => isset($composer['require']['objecting/object']),
                'constraint' => $composer['require']['objecting/object'] ?? null,
            ],
            'usage' => [
                'app_objecting_reference_count' => count($objectingReferences),
                'app_objecting_references' => array_slice($objectingReferences, 0, 50),
            ],
            'findings' => $findings,
            'summary' => [
                'system_field_candidate_count' => count(array_filter($findings, static fn (array $finding): bool => 'system_field_candidate' === $finding['classification'])),
                'business_exception_candidate_count' => count(array_filter($findings, static fn (array $finding): bool => 'business_exception_candidate' === $finding['classification'])),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function readComposer(): array
    {
        $path = $this->root.DIRECTORY_SEPARATOR.'composer.json';
        if (!is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    /** @return list<string> */
    private function findObjectingReferences(): array
    {
        $matches = [];
        foreach ($this->collectPhpFiles($this->root.DIRECTORY_SEPARATOR.'src') as $file) {
            $contents = (string) file_get_contents($file);
            if (str_contains($contents, 'App\\Objecting\\')) {
                $matches[] = $this->relativePath($file);
            }
        }

        sort($matches);

        return $matches;
    }

    /** @return list<string> */
    private function collectPhpFiles(string $dir): array
    {
        if (!is_dir($dir)) {
            return [];
        }

        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if ($file instanceof SplFileInfo && $file->isFile() && 'php' === $file->getExtension()) {
                $files[] = $file->getPathname();
            }
        }

        sort($files);

        return $files;
    }

    /** @return list<string> */
    private function extractPrivateFields(string $contents): array
    {
        $fields = [];
        $tokens = token_get_all($contents);
        for ($i = 0, $count = count($tokens); $i < $count; ++$i) {
            $token = $tokens[$i];
            if (!is_array($token) || T_PRIVATE !== $token[0]) {
                continue;
            }

            $variable = $this->nextVariable($tokens, $i);
            if (null !== $variable) {
                $fields[] = ltrim($variable, '$');
            }
        }

        return array_values(array_unique($fields));
    }

    /** @param list<mixed> $tokens */
    private function nextVariable(array $tokens, int $offset): ?string
    {
        for ($i = $offset + 1, $count = count($tokens); $i < $count; ++$i) {
            $token = $tokens[$i];
            if (is_array($token) && T_VARIABLE === $token[0]) {
                return $token[1];
            }
            if (';' === $token || '{' === $token) {
                return null;
            }
        }

        return null;
    }

    private function relativePath(string $file): string
    {
        return str_replace('\\', '/', ltrim(substr($file, strlen($this->root)), DIRECTORY_SEPARATOR));
    }
}

$audit = new RollingObjectingAdoptionAudit(dirname(__DIR__, 2));
$report = $audit->run();

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
exit(0);
