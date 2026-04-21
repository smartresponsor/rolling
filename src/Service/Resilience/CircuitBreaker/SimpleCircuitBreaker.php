<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Resilience\CircuitBreaker;

use App\Rolling\ServiceInterface\Resilience\CircuitBreakerInterface;
use App\Rolling\ServiceInterface\Resilience\Time\ClockInterface;

final class SimpleCircuitBreaker implements CircuitBreakerInterface
{
    private string $state = self::CLOSED;
    private int $failCount = 0;
    private int $lastFailAt = 0;
    private int $halfOpenProbeAt = 0;

    /**
     * @param ClockInterface $clock
     * @param int            $threshold
     * @param int            $windowMs
     * @param int            $coolDownMs
     */
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly int $threshold = 5,
        private readonly int $windowMs = 10_000,
        private readonly int $coolDownMs = 5_000,
    ) {
    }

    /**
     * @return bool
     */
    public function allow(): bool
    {
        $now = $this->clock->nowMs();
        if (self::OPEN === $this->state) {
            if ($now - $this->lastFailAt >= $this->coolDownMs) {
                $this->state = self::HALF_OPEN;
                $this->halfOpenProbeAt = $now;

                return true; // allow one probe
            }

            return false;
        }
        if (self::HALF_OPEN === $this->state) {
            // allow single in-flight probe after coolDown
            return $now - $this->halfOpenProbeAt >= $this->coolDownMs;
        }

        // CLOSED
        return true;
    }

    /**
     * @return void
     */
    public function onSuccess(): void
    {
        $this->state = self::CLOSED;
        $this->failCount = 0;
    }

    /**
     * @param \Throwable $e
     *
     * @return void
     */
    public function onFailure(\Throwable $e): void
    {
        $now = $this->clock->nowMs();
        $this->lastFailAt = $now;
        // decay window
        if ($this->failCount > 0 && $now - $this->lastFailAt > $this->windowMs) {
            $this->failCount = 0;
        }
        ++$this->failCount;
        if ($this->failCount >= $this->threshold) {
            $this->state = self::OPEN;
        }
    }

    /**
     * @return string
     */
    public function state(): string
    {
        return $this->state;
    }

    /**
     * @return array
     */
    public function getMetrics(): array
    {
        return [
            'state' => $this->state,
            'failCount' => $this->failCount,
            'threshold' => $this->threshold,
            'windowMs' => $this->windowMs,
            'coolDownMs' => $this->coolDownMs,
            'lastFailAt' => $this->lastFailAt,
        ];
    }
}
