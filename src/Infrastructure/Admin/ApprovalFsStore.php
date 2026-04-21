<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Rolling\Infrastructure\Admin;

use App\Rolling\ServiceInterface\Admin\ApprovalStoreInterface;

final class ApprovalFsStore implements ApprovalStoreInterface
{
    public function __construct(private readonly string $baseDir)
    {
    }

    public function create(array $row): string
    {
        @mkdir($this->baseDir.'/approvals', 0775, true);

        try {
            $id = bin2hex(random_bytes(8));
        } catch (\Exception $e) {
            error_log('ApprovalFsStore::create random_bytes fallback: '.$e->getMessage());
            $id = 'apr_'.str_replace('.', '', (string) microtime(true));
        }

        $row['id'] = $id;
        $row['createdAt'] = gmdate('c');
        $row['status'] = 'pending';
        $row['approvals'] = [];
        $row['rejections'] = [];
        file_put_contents($this->baseDir."/approvals/$id.json", json_encode($row, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        $this->audit(['type' => 'create', 'id' => $id, 'row' => $row]);

        return $id;
    }

    public function load(string $id): ?array
    {
        $f = $this->baseDir."/approvals/$id.json";
        if (!is_file($f)) {
            return null;
        }

        $j = json_decode((string) file_get_contents($f), true);

        return is_array($j) ? $j : null;
    }

    public function save(string $id, array $row): void
    {
        @mkdir($this->baseDir.'/approvals', 0775, true);
        file_put_contents($this->baseDir."/approvals/$id.json", json_encode($row, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        $this->audit(['type' => 'save', 'id' => $id, 'row' => $row]);
    }

    private function audit(array $row): void
    {
        @mkdir($this->baseDir.'/audit', 0775, true);
        file_put_contents($this->baseDir.'/audit/admin.ndjson', json_encode(['ts' => gmdate('c')] + $row).PHP_EOL, FILE_APPEND);
    }
}
