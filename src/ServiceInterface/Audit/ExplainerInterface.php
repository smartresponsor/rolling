<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Audit;

use App\Rolling\Service\Audit\Dto\AuditDecisionInputDto;
use App\Rolling\Service\Audit\Dto\AuditDecisionResultDto;

interface ExplainerInterface
{
    /**
     * Build structured explanation (tree) from input+result.
     *
     * @return array<string,mixed> JSON-serializable structure
     */
    public function explain(AuditDecisionInputDto $in, AuditDecisionResultDto $res): array;
}
