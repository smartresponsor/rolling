<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Audit;

use App\Service\Audit\Dto\DecisionRecord;

/**
 *
 */

/**
 *
 */
interface AuditLoggerInterface
{
    /**
     * @param \App\Service\Audit\Dto\DecisionRecord $rec
     * @return void
     */
    public function log(DecisionRecord $rec): void;
}
