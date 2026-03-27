<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace Resilience\Backoff;

use App\ServiceInterface\Role\Resilience\BackoffStrategyInterface;

/**
 *
 */

/**
 *
 */
final class ExponentialJitterBackoff implements BackoffStrategyInterface
{
    /**
     * @param int $baseMs
     * @param int $maxMs
     */
    public function __construct(
        private readonly int $baseMs = 50,
        private readonly int $maxMs = 2000,
    ) {}

    /**
     * @param int $attempt
     * @return int
     */
    public function nextDelayMs(int $attempt): int
    {
        $cap = min($this->maxMs, $this->baseMs * (1 << max(0, $attempt - 1)));
        try {
            $jitter = random_int(0, (int) floor($cap * 0.3));
        } catch (\Exception $e) {
        }
        return $cap - (int) floor($cap * 0.3) + $jitter;
    }

    /**
     * @return void
     */
    public function reset(): void {}
}
