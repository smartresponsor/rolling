<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace src\ServiceInterface\Role\Audit;

/**
 *
 */

/**
 *
 */
interface AuditTrailInterface
{
    /**
     * @param array $rec
     */
    public function write(array $rec): void;
}
