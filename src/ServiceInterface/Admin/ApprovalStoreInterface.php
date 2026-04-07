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
interface ApprovalStoreInterface
{
    /** Create a new approval request and return its ID. */
    public function create(array $row): string;

    /** Load approval by ID. @return array<string,mixed>|null */
    public function load(string $id): ?array;

    /** Persist changes. */
    public function save(string $id, array $row): void;
}
