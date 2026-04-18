<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Infrastructure\Key;

use Exception;
use App\ServiceInterface\Key\KeyProviderInterface;

/**
 * File-based key provider with naive rotation.
 * Storage layout: var/keys/<tenant>/key_<kid>.json
 */
final class FileKeyProvider implements KeyProviderInterface
{
    /**
     * @param string $dir
     */
    public function __construct(private readonly string $dir = __DIR__ . '/../../../var/keys')
    {
        if (!is_dir($this->dir)) {
            @mkdir($this->dir, 0775, true);
        }
    }

    /**
     * @param string $tenant
     * @return string
     */
    private function tenantDir(string $tenant): string
    {
        $d = $this->dir . '/' . $tenant;
        if (!is_dir($d)) {
            @mkdir($d, 0775, true);
        }
        return $d;
    }

    /** @return array{kid:string, material:string} */
    public function getActive(string $tenant): array
    {
        $d = $this->tenantDir($tenant);
        $activePath = $d . '/active.json';
        if (is_file($activePath)) {
            $j = json_decode((string) file_get_contents($activePath), true);
            if (is_array($j) && isset($j['kid'])) {
                $kp = $this->getById($tenant, (string) $j['kid']);
                if ($kp) {
                    return $kp;
                }
            }
        }
        // bootstrap
        return $this->rotate($tenant);
    }

    /**
     * @param string $tenant
     * @param string $kid
     * @return string[]|null
     */
    public function getById(string $tenant, string $kid): ?array
    {
        $p = $this->tenantDir($tenant) . '/key_' . $kid . '.json';
        if (!is_file($p)) {
            return null;
        }
        $j = json_decode((string) file_get_contents($p), true);
        if (!is_array($j)) {
            return null;
        }
        return ['kid' => (string) $j['kid'], 'material' => (string) $j['material']];
    }

    /**
     * @param string $tenant
     * @return array
     */
    public function rotate(string $tenant): array
    {
        $d = $this->tenantDir($tenant);
        try {
            $kid = date('Ymd_His') . '_' . substr(bin2hex(random_bytes(4)), 0, 8);
        } catch (Exception $e) {
            error_log('FileKeyProvider::rotate kid fallback: ' . $e->getMessage());
            $kid = date('Ymd_His') . '_fallback';
        }
        try {
            $material = base64_encode(random_bytes(32));
        } catch (Exception $e) {
            error_log('FileKeyProvider::rotate material fallback: ' . $e->getMessage());
            $material = base64_encode(hash('sha256', $tenant . '|' . microtime(true), true));
        } // 256-bit HMAC key
        $rec = ['kid' => $kid, 'material' => $material, 'ts' => date('c')];
        file_put_contents($d . '/key_' . $kid . '.json', json_encode($rec, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        file_put_contents($d . '/active.json', json_encode(['kid' => $kid], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        return ['kid' => $kid, 'material' => $material];
    }
}
