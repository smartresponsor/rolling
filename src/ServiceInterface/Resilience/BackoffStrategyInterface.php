<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Resilience;

interface BackoffStrategyInterface
{
    public function nextDelayMs(int $attempt): int;

    public function reset(): void;
}
