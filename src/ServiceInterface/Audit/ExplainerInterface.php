<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Audit;

use App\Rolling\DTO\Audit\AuditDecisionInputDTO;
use App\Rolling\DTO\Audit\AuditDecisionResultDTO;

interface ExplainerInterface
{
    /**
     * Build structured explanation (tree) from input+result.
     *
     * @return array<string,mixed> JSON-serializable structure
     */
    public function explain(AuditDecisionInputDTO $in, AuditDecisionResultDTO $res): array;
}
