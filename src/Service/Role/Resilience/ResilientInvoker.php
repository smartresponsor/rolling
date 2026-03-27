<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace Resilience;

use App\ServiceInterface\Role\Resilience\BackoffStrategyInterface;
use App\ServiceInterface\Role\Resilience\CircuitBreakerInterface;
use App\ServiceInterface\Role\Resilience\ResilientInvokerInterface;
use App\ServiceInterface\Role\Resilience\Time\ClockInterface;
use App\ServiceInterface\Role\Resilience\Time\SleeperInterface;
use RuntimeException;
use Throwable;

/**
 *
 */

/**
 *
 */
final class ResilientInvoker implements ResilientInvokerInterface
{
    /**
     * @param \App\ServiceInterface\Role\Resilience\CircuitBreakerInterface $breaker
     * @param \App\ServiceInterface\Role\Resilience\BackoffStrategyInterface $backoff
     * @param \App\ServiceInterface\Role\Resilience\Time\ClockInterface $clock
     * @param \App\ServiceInterface\Role\Resilience\Time\SleeperInterface $sleeper
     */
    public function __construct(
        private readonly CircuitBreakerInterface  $breaker,
        private readonly BackoffStrategyInterface $backoff,
        private readonly ClockInterface           $clock,
        private readonly SleeperInterface         $sleeper,
    ) {}

    /**
     * @throws \Throwable
     */
    /**
     * @param callable $fn
     * @param array $options
     * @return mixed
     * @throws \Throwable
     */
    /**
     * @param callable $fn
     * @param array $options
     * @return mixed
     * @throws \Throwable
     */
    public function invoke(callable $fn, array $options = [])
    {
        $maxAttempts = (int) ($options['maxAttempts'] ?? 5);
        $classifyPermanent = $options['classifyPermanent'] ?? [ErrorClassifier::class, 'isPermanent'];

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            if (!$this->breaker->allow()) {
                throw new RuntimeException('Circuit open, request not allowed');
            }
            try {
                $res = $fn();
                $this->breaker->onSuccess();
                return $res;
            } catch (Throwable $e) {
                $this->breaker->onFailure($e);
                if (is_callable($classifyPermanent) && $classifyPermanent($e)) {
                    throw $e;
                }
                if ($attempt >= $maxAttempts) {
                    throw $e;
                }
                $delay = $this->backoff->nextDelayMs($attempt);
                $this->sleeper->sleepMs($delay);
            }
        }
        throw new RuntimeException('Unreachable');
    }
}
