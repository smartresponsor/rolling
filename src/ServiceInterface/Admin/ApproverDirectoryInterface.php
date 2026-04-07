<?php
declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\ServiceInterface\Admin;

/**
 *
 */

/**
 *
 */
interface ApproverDirectoryInterface
{
    /** Can this subject approve given relation/resource? */
    public function canApprove(string $tenant, string $subject, string $relation, string $resource): bool;

    /** Resolve delegate for a subject (if any, and valid). */
    public function resolveDelegate(string $tenant, string $subject): ?string;
}
