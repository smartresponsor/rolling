<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Resilience\Time;

/**
 *
 */

/**
 *
 */
interface SleeperInterface
{
    /**
     * @param int $ms
     * @return void
     */
    public function sleepMs(int $ms): void;
}
