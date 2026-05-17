<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Pdp;

use App\Rolling\Service\Pdp\Dto\PdpDecisionRequestDto;
use App\Rolling\Service\Pdp\Dto\PdpDecisionResponseDto;

/**
 * Batch decision interface for PDP v3.
 */
interface BatchDecisionInterface
{
    /**
     * @param PdpDecisionRequestDto[] $requests
     *
     * @return PdpDecisionResponseDto[]
     */
    public function decideMany(array $requests): array;
}
