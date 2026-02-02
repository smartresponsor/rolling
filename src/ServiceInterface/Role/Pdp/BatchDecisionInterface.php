<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace src\ServiceInterface\Role\Pdp;

use App\Service\Role\Pdp\Dto\DecisionRequest;
use App\Service\Role\Pdp\Dto\DecisionResponse;

/**
 * Batch decision interface for PDP v3.
 */
interface BatchDecisionInterface
{
    /**
     * @param DecisionRequest[] $requests
     * @return DecisionResponse[]
     */
    public function decideMany(array $requests): array;
}
