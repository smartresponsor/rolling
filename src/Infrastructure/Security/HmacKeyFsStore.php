<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Infrastructure\Security;

use App\InfrastructureInterface\Security\KeyStoreInterface;
use Exception;

/**
 *
 */

/**
 *
 */
final class HmacKeyFsStore implements KeyStoreInterface
{
    /**
     * @param string $baseDir
     */
    public function __construct(private readonly string $baseDir) {} // var/keys/<tenant>/hmac/{current.key,archive/*.key}

    /**
     * @param string $tenant
     * @return string
     */
    private function tdir(string $tenant): string
    {
        return rtrim($this->baseDir, '/') . "/$tenant";
    }

    /**
     * @param string $tenant
     * @return array|string[]
     */
    public function currentHmac(string $tenant): array
    {
        $dir = $this->tdir($tenant) . '/hmac';
        @mkdir($dir, 0775, true);
        $cur = $dir . '/current.key';
        if (!is_file($cur)) {
            $kid = 'kid-' . date('YmdHis');
            try {
                $key = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                error_log('HmacKeyFsStore::currentHmac key fallback: ' . $e->getMessage());
                $key = hash('sha256', $tenant . '|' . microtime(true));
            }
            file_put_contents($cur, json_encode(['kid' => $kid, 'key' => $key], JSON_UNESCAPED_SLASHES));
            return ['kid' => $kid, 'key' => $key];
        }
        $j = json_decode((string) file_get_contents($cur), true);
        return ['kid' => (string) $j['kid'], 'key' => (string) $j['key']];
    }

    /**
     * @param string $tenant
     * @param string|null $note
     * @return string
     */
    public function rotateHmac(string $tenant, ?string $note = null): string
    {
        $dir = $this->tdir($tenant) . '/hmac';
        @mkdir($dir . '/archive', 0775, true);
        $cur = $this->currentHmac($tenant);
        // archive old
        file_put_contents($dir . '/archive/' . $cur['kid'] . '.key', json_encode($cur + ['note' => $note, 'archivedAt' => gmdate('c')], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        // write new
        $kid = 'kid-' . date('YmdHis');
        try {
            $key = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            error_log('HmacKeyFsStore::rotateHmac key fallback: ' . $e->getMessage());
            $key = hash('sha256', $tenant . '|rotate|' . microtime(true));
        }
        file_put_contents($dir . '/current.key', json_encode(['kid' => $kid, 'key' => $key, 'rotatedAt' => gmdate('c')], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        return $kid;
    }

    /**
     * @param string $tenant
     * @param string $kid
     * @return string|null
     */
    public function loadHmac(string $tenant, string $kid): ?string
    {
        $dir = $this->tdir($tenant) . '/hmac';
        $cur = $this->currentHmac($tenant);
        if ($kid === $cur['kid']) {
            return $cur['key'];
        }
        $f = $dir . '/archive/' . $kid . '.key';
        if (!is_file($f)) {
            return null;
        }
        $j = json_decode((string) file_get_contents($f), true);
        return is_array($j) ? (string) ($j['key'] ?? null) : null;
    }

    /**
     * @param string $tenant
     * @return array[]
     */
    public function jwks(string $tenant): array
    {
        $file = $this->tdir($tenant) . '/jwks.json';
        $j = is_file($file) ? json_decode((string) file_get_contents($file), true) : ['keys' => []];
        return is_array($j) ? $j : ['keys' => []];
    }

    /**
     * @param string $tenant
     * @param array $jwks
     * @return void
     */
    public function putJwks(string $tenant, array $jwks): void
    {
        $file = $this->tdir($tenant) . '/jwks.json';
        @mkdir(dirname($file), 0775, true);
        file_put_contents($file, json_encode($jwks, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
}
