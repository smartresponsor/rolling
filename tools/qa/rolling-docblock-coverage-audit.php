<?php

declare(strict_types=1);

final class RollingDocblockCoverageAudit
{
    private const SOURCE_DIRS = [
        'src/Controller',
        'src/Command',
        'src/DTO',
        'src/Entity',
        'src/EntityInterface',
        'src/InfrastructureInterface',
        'src/Repository',
        'src/RepositoryInterface',
        'src/Service',
        'src/ServiceInterface',
        'src/Value',
    ];

    private string $root;

    public function __construct(string $root)
    {
        $this->root = rtrim($root, DIRECTORY_SEPARATOR);
    }

    /** @return array<string, mixed> */
    public function run(): array
    {
        $files = $this->collectFiles();
        $classCount = 0;
        $documentedClassCount = 0;
        $publicMethodCount = 0;
        $documentedPublicMethodCount = 0;
        $publicPropertyCount = 0;
        $documentedPublicPropertyCount = 0;
        $missing = [];

        foreach ($files as $file) {
            $tokens = token_get_all((string) file_get_contents($file));
            $relative = $this->relativePath($file);
            $lastDocComment = null;

            for ($i = 0, $count = count($tokens); $i < $count; ++$i) {
                $token = $tokens[$i];
                if (is_array($token) && T_DOC_COMMENT === $token[0]) {
                    $lastDocComment = trim($token[1]);
                    continue;
                }

                if (!$this->isNamedToken($token, [T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM])) {
                    if ($this->isVisibilityToken($token) && $this->nextNamedTokenIs($tokens, $i, T_FUNCTION)) {
                        $name = $this->nextNameAfter($tokens, $i, T_FUNCTION) ?? 'anonymous';
                        if ($this->isIgnoredBoilerplateMethod($name)) {
                            $lastDocComment = null;
                            continue;
                        }

                        ++$publicMethodCount;
                        if ($this->hasUsefulDocComment($lastDocComment)) {
                            ++$documentedPublicMethodCount;
                        } else {
                            $missing[] = ['type' => 'public_method', 'file' => $relative, 'name' => $name];
                        }
                        $lastDocComment = null;
                        continue;
                    }

                    if ($this->isVisibilityToken($token) && $this->nextNamedTokenIs($tokens, $i, T_VARIABLE) && !$this->isConstructorPromotedProperty($tokens, $i)) {
                        ++$publicPropertyCount;
                        $name = $this->nextVariableAfter($tokens, $i) ?? '$unknown';
                        if ($this->hasUsefulDocComment($lastDocComment)) {
                            ++$documentedPublicPropertyCount;
                        } else {
                            $missing[] = ['type' => 'public_property', 'file' => $relative, 'name' => $name];
                        }
                        $lastDocComment = null;
                        continue;
                    }

                    continue;
                }

                if (T_CLASS === $token[0] && $this->previousSignificantTokenIs($tokens, $i, T_DOUBLE_COLON)) {
                    $lastDocComment = null;
                    continue;
                }

                $className = $this->nextString($tokens, $i);
                if (null === $className) {
                    continue;
                }

                ++$classCount;
                if ($this->hasUsefulDocComment($lastDocComment)) {
                    ++$documentedClassCount;
                } else {
                    $missing[] = ['type' => 'class', 'file' => $relative, 'name' => $className];
                }
                $lastDocComment = null;
                $classStack[] = $className;
            }
        }

        return [
            'status' => 'report',
            'scope' => self::SOURCE_DIRS,
            'summary' => [
                'classes' => $this->metric($classCount, $documentedClassCount),
                'public_methods' => $this->metric($publicMethodCount, $documentedPublicMethodCount),
                'public_properties' => $this->metric($publicPropertyCount, $documentedPublicPropertyCount),
            ],
            'missing' => array_slice($missing, 0, 250),
            'missing_truncated' => count($missing) > 250,
        ];
    }

    /** @return list<string> */
    private function collectFiles(): array
    {
        $files = [];
        foreach (self::SOURCE_DIRS as $dir) {
            $path = $this->root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $dir);
            if (!is_dir($path)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
            foreach ($iterator as $file) {
                if ($file instanceof SplFileInfo && $file->isFile() && 'php' === $file->getExtension()) {
                    $files[] = $file->getPathname();
                }
            }
        }

        sort($files);

        return $files;
    }

    /** @return array{total:int, documented:int, missing:int, coverage:float} */
    private function metric(int $total, int $documented): array
    {
        return [
            'total' => $total,
            'documented' => $documented,
            'missing' => max(0, $total - $documented),
            'coverage' => 0 === $total ? 100.0 : round(($documented / $total) * 100, 2),
        ];
    }

    private function hasUsefulDocComment(?string $docComment): bool
    {
        if (null === $docComment) {
            return false;
        }

        $normalized = preg_replace('/\s+/', ' ', trim(str_replace(['/**', '*/', '*'], ' ', $docComment)));

        return is_string($normalized) && strlen(trim($normalized)) >= 12;
    }

    /** @param list<mixed> $tokenIds */
    private function isNamedToken(mixed $token, array $tokenIds): bool
    {
        return is_array($token) && in_array($token[0], $tokenIds, true);
    }

    private function isVisibilityToken(mixed $token): bool
    {
        return is_array($token) && in_array($token[0], [T_PUBLIC, T_PROTECTED], true);
    }

    private function isIgnoredBoilerplateMethod(string $name): bool
    {
        if ('__construct' === $name || '__toString' === $name) {
            return true;
        }

        if (preg_match('/^(get|set|is|has|can)[A-Z]/', $name)) {
            return true;
        }

        return in_array($name, [
            'id',
            'key',
            'type',
            'value',
            'tenantId',
            'resourceId',
        ], true);
    }

    /** @param list<mixed> $tokens */
    private function nextNamedTokenIs(array $tokens, int $offset, int $tokenId): bool
    {
        for ($i = $offset + 1, $count = count($tokens); $i < $count; ++$i) {
            $token = $tokens[$i];
            if (is_array($token) && in_array($token[0], [T_WHITESPACE, T_STATIC, T_FINAL, T_ABSTRACT, T_READONLY], true)) {
                continue;
            }

            return is_array($token) && $tokenId === $token[0];
        }

        return false;
    }

    /** @param list<mixed> $tokens */
    private function nextNameAfter(array $tokens, int $offset, int $anchorToken): ?string
    {
        $seenAnchor = false;
        for ($i = $offset + 1, $count = count($tokens); $i < $count; ++$i) {
            $token = $tokens[$i];
            if (!$seenAnchor) {
                if (is_array($token) && $anchorToken === $token[0]) {
                    $seenAnchor = true;
                }
                continue;
            }

            if (is_array($token) && T_STRING === $token[0]) {
                return $token[1];
            }
        }

        return null;
    }

    /** @param list<mixed> $tokens */
    private function nextVariableAfter(array $tokens, int $offset): ?string
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

    /** @param list<mixed> $tokens */
    private function nextString(array $tokens, int $offset): ?string
    {
        for ($i = $offset + 1, $count = count($tokens); $i < $count; ++$i) {
            $token = $tokens[$i];
            if (is_array($token) && T_STRING === $token[0]) {
                return $token[1];
            }
        }

        return null;
    }

    /** @param list<mixed> $tokens */
    private function isConstructorPromotedProperty(array $tokens, int $offset): bool
    {
        for ($i = $offset - 1; $i >= 0; --$i) {
            $token = $tokens[$i];
            if (is_array($token) && T_FUNCTION === $token[0]) {
                return '__construct' === $this->nextString($tokens, $i);
            }

            if (';' === $token || '}' === $token) {
                return false;
            }
        }

        return false;
    }

    /** @param list<mixed> $tokens */
    private function previousSignificantTokenIs(array $tokens, int $offset, int $tokenId): bool
    {
        for ($i = $offset - 1; $i >= 0; --$i) {
            $token = $tokens[$i];
            if (is_array($token) && in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }

            return is_array($token) && $tokenId === $token[0];
        }

        return false;
    }

    private function relativePath(string $file): string
    {
        return str_replace('\\', '/', ltrim(substr($file, strlen($this->root)), DIRECTORY_SEPARATOR));
    }
}

$audit = new RollingDocblockCoverageAudit(dirname(__DIR__, 2));
$report = $audit->run();

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
exit(0);
