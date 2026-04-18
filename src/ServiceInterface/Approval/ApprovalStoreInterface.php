<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Approval;

/**
 * Four-eyes approval store.
 */
interface ApprovalStoreInterface
{
    /** @return string approval id */
    public function create(array $case): string;

    /** @return array<string,mixed>|null */
    public function read(string $id): ?array;

    /**
     * @param string $id
     * @param array $by
     * @return void
     */
    public function approve(string $id, array $by): void;

    /**
     * @param string $id
     * @param array $by
     * @return void
     */
    public function reject(string $id, array $by): void;
}
