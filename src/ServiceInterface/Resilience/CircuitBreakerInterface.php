<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Resilience;

interface CircuitBreakerInterface
{
    public const string CLOSED = 'closed';
    public const string OPEN = 'open';
    public const string HALF_OPEN = 'half_open';

    public function allow(): bool;

    public function onSuccess(): void;

    public function onFailure(\Throwable $e): void;

    public function state(): string;

    /**
     * @return array<string,mixed>
     */
    public function getMetrics(): array;
}
