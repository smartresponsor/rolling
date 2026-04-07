<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Resilience;

/**
 *
 */

/**
 *
 */
interface BackoffStrategyInterface
{
    /**
     * @param int $attempt
     * @return int
     */
    public function nextDelayMs(int $attempt): int;

    /**
     * @return void
     */
    public function reset(): void;
}
