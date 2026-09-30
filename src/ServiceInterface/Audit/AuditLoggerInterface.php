<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Audit;

use App\Rolling\DTO\Audit\AuditDecisionRecordDTO;

interface AuditLoggerInterface
{
    public function log(AuditDecisionRecordDTO $rec): void;
}
