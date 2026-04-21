<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\InfrastructureInterface\Audit;

interface AuditRepositoryInterface
{
    /**
     * Persist audit record as associative array.
     *
     * @param array $data
     */
    public function save(array $data): void;
}
