<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Role\Audit;

use Audit\Dto\DecisionInput;
use Audit\Dto\DecisionResult;

/**
 *
 */

/**
 *
 */
interface ExplainerInterface
{
    /**
     * Build structured explanation (tree) from input+result.
     * @return array<string,mixed> JSON-serializable structure
     */
    public function explain(DecisionInput $in, DecisionResult $res): array;
}
