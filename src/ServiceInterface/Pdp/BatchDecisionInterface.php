<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Pdp;

use App\Rolling\DTO\Pdp\PdpDecisionRequestDTO;
use App\Rolling\DTO\Pdp\PdpDecisionResponseDTO;

/**
 * Batch decision interface for PDP v3.
 */
interface BatchDecisionInterface
{
    /**
     * @param PdpDecisionRequestDTO[] $requests
     *
     * @return PdpDecisionResponseDTO[]
     */
    public function decideMany(array $requests): array;
}
