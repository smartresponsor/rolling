<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Rolling\Infrastructure\Admin;

use App\Rolling\ServiceInterface\Admin\ApproverDirectoryInterface;

final class ApproverFsDirectory implements ApproverDirectoryInterface
{
    public function __construct(private readonly string $baseDir)
    {
    } // var/admin/approvers.json + delegations.json

    public function canApprove(string $tenant, string $subject, string $relation, string $resource): bool
    {
        $file = $this->baseDir.'/approvers.json';
        $j = is_file($file) ? json_decode((string) file_get_contents($file), true) : [];
        $arr = is_array($j) ? $j : [];
        $allow = (array) ($arr[$tenant]['allow'] ?? []);

        return in_array($subject, $allow, true);
    }

    public function resolveDelegate(string $tenant, string $subject): ?string
    {
        $file = $this->baseDir.'/delegations.json';
        $j = is_file($file) ? json_decode((string) file_get_contents($file), true) : [];
        $arr = is_array($j) ? $j : [];
        $d = (array) ($arr[$tenant] ?? []);
        $now = time();
        foreach ($d as $row) {
            if (($row['from'] ?? '') === $subject && ($row['until'] ?? 0) >= $now) {
                return (string) ($row['to'] ?? null);
            }
        }

        return null;
    }
}
