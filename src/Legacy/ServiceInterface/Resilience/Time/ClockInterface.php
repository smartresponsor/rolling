<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Legacy\ServiceInterface\Resilience\Time;

/**
 *
 */

/**
 *
 */
interface ClockInterface
{
    /**
     * @return int
     */
    public function nowMs(): int;
}
