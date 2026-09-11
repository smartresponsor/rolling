<?php

declare(strict_types=1);

/**
 * Resolve the exact Git commit for repository-owned RC evidence without invoking Git.
 *
 * CI-provided commit identifiers take precedence. Local repositories are resolved from
 * HEAD, loose refs, or packed refs, including worktrees whose .git entry is a gitfile.
 *
 * @param array<string, scalar|null>|null $environment
 */
function rollingResolveSourceRevision(string $projectRoot, ?array $environment = null): ?string
{
    $normalizeSha = static function (mixed $value): ?string {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        return preg_match('/^[0-9a-f]{40}$/i', $value) === 1 ? strtolower($value) : null;
    };

    foreach (['GITHUB_SHA', 'CI_COMMIT_SHA'] as $variable) {
        $value = $environment === null ? getenv($variable) : ($environment[$variable] ?? null);
        $sha = $normalizeSha($value);
        if ($sha !== null) {
            return $sha;
        }
    }

    $gitEntry = rtrim($projectRoot, '/\\').'/.git';
    $gitDir = null;

    if (is_dir($gitEntry)) {
        $gitDir = $gitEntry;
    } elseif (is_file($gitEntry)) {
        $gitFile = trim((string) file_get_contents($gitEntry));
        if (preg_match('/^gitdir:\s*(.+)$/i', $gitFile, $matches) === 1) {
            $candidate = trim($matches[1]);
            $isAbsolute = str_starts_with($candidate, '/') || preg_match('/^[A-Za-z]:[\\\\\/]/', $candidate) === 1;
            $gitDir = $isAbsolute ? $candidate : dirname($gitEntry).'/'.$candidate;
        }
    }

    if ($gitDir === null || !is_dir($gitDir)) {
        return null;
    }

    $headPath = rtrim($gitDir, '/\\').'/HEAD';
    if (!is_file($headPath)) {
        return null;
    }

    $head = trim((string) file_get_contents($headPath));
    $detachedSha = $normalizeSha($head);
    if ($detachedSha !== null) {
        return $detachedSha;
    }

    if (!str_starts_with($head, 'ref: ')) {
        return null;
    }

    $ref = trim(substr($head, 5));
    if (preg_match('~^refs/[A-Za-z0-9._/-]+$~', $ref) !== 1 || str_contains($ref, '..')) {
        return null;
    }

    $looseRefPath = rtrim($gitDir, '/\\').'/'.$ref;
    if (is_file($looseRefPath)) {
        $looseSha = $normalizeSha((string) file_get_contents($looseRefPath));
        if ($looseSha !== null) {
            return $looseSha;
        }
    }

    $packedRefsPath = rtrim($gitDir, '/\\').'/packed-refs';
    if (!is_file($packedRefsPath)) {
        return null;
    }

    foreach (file($packedRefsPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        if (str_starts_with($line, '#') || str_starts_with($line, '^')) {
            continue;
        }

        [$packedSha, $packedRef] = array_pad(preg_split('/\s+/', trim($line), 2) ?: [], 2, null);
        if ($packedRef === $ref) {
            return $normalizeSha($packedSha);
        }
    }

    return null;
}
