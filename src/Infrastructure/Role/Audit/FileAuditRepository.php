<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Infra\Role\Audit;

use App\InfraInterface\Role\Audit\AuditRepositoryInterface;

/**
 *
 */

/**
 *
 */
final class FileAuditRepository implements AuditRepositoryInterface
{
    /**
     * @param string $path
     */
    public function __construct(private readonly string $path)
    {
    }

    /**
     * @param array $data
     * @return void
     */
    public function save(array $data): void
    {
        @mkdir(dirname($this->path), 0775, true);
        file_put_contents($this->path, json_encode($data, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND);
    }
}
