<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Role\Resilience;

/**
 *
 */

/**
 *
 */
interface ResilientInvokerInterface
{
    /**
     * Invoke a callable with retries, backoff and circuit-breaker.
     * @param callable $fn A function to call (may throw exceptions)
     * @param array $options Options like maxAttempts, classifyPermanent
     * @return mixed
     */
    public function invoke(callable $fn, array $options = []);
}
