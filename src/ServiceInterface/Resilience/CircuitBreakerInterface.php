<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Resilience;

use Throwable;

/**
 *
 */

/**
 *
 */
interface CircuitBreakerInterface
{
    public const CLOSED = 'closed';
    public const OPEN = 'open';
    public const HALF_OPEN = 'half_open';

    /**
     * @return bool
     */
    public function allow(): bool;

    /**
     * @return void
     */
    public function onSuccess(): void;

    /**
     * @param \Throwable $e
     * @return void
     */
    public function onFailure(Throwable $e): void;

    /**
     * @return string
     */
    public function state(): string;

    /**
     * @return array<string,mixed>
     */
    public function getMetrics(): array;
}
