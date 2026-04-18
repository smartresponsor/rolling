<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Infrastructure\Audit;

use App\ServiceInterface\Audit\AuditTrailInterface;

/**
 *
 */

/**
 *
 */
final class FileAuditTrail implements AuditTrailInterface
{
    /**
     * @param string $dir
     */
    public function __construct(private readonly string $dir = __DIR__ . '/../../../../var/audit')
    {
        if (!is_dir($this->dir)) {
            @mkdir($this->dir, 0775, true);
        }
    }

    /**
     * @param array $rec
     */
    public function write(array $rec): void
    {
        $day = date('Y-m-d');
        $p = $this->dir . '/audit_' . $day . '.jsonl';
        $rec['ts'] = date('c');
        file_put_contents($p, json_encode($rec, JSON_UNESCAPED_SLASHES) . "\n", FILE_APPEND);
    }
}
