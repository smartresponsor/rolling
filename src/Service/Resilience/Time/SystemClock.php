<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Resilience\Time;

use App\Rolling\ServiceInterface\Resilience\Time\ClockInterface;

final class SystemClock implements ClockInterface
{
    /**
     * @return int
     */
    public function nowMs(): int
    {
        return (int) floor(microtime(true) * 1000);
    }
}
