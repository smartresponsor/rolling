<?php

declare(strict_types=1);

namespace App\Legacy\Resilience;

use App\Net\Http\RemoteHttpException;
use App\Policy\Obligation\Obligation;
use App\Policy\Obligation\Obligations;
use App\Policy\V2\DecisionWithObligations;
use App\PolicyInterface\PdpV2Interface;
use Throwable;

/**
 * Декоратор PDP v2 с Circuit Breaker.
 */
final class CircuitBreakingPdpV2 implements PdpV2Interface
{
    private string $id;
    private int $state = 0; // 0=closed, 1=open, 2=half_open
    private int $failures = 0;
    private int $openedAt = 0;
    private int $retries = 0;
    private bool $probeTaken = false;

    /**
     * @param \PolicyInterface\Role\PdpV2Interface $inner
     * @param string $breakerId
     * @param int $failureThreshold
     * @param int $openBaseSeconds
     * @param int $openMaxSeconds
     * @param \App\Legacy\Resilience\Clock $clock
     */
    public function __construct(
        private readonly PdpV2Interface $inner,
        string                          $breakerId = 'role.pdp.v2',
        private readonly int            $failureThreshold = 3,
        private readonly int            $openBaseSeconds = 5,
        private readonly int            $openMaxSeconds = 120,
        private readonly Clock          $clock = new SystemClock(),
    ) {
        $this->id = $breakerId;
    }

    /**
     * @param \App\Entity\Role\SubjectId $s
     * @param \App\Entity\Role\PermissionKey $a
     * @param \App\Entity\Role\Scope $sc
     * @param array $context
     * @return \Policy\Role\V2\DecisionWithObligations
     * @throws \Throwable
     */
    public function check(\App\Entity\Role\SubjectId $s, \App\Entity\Role\PermissionKey $a, \App\Entity\Role\Scope $sc, array $context = []): DecisionWithObligations
    {
        $now = $this->clock->now();

        if ($this->state === 1) { // open
            $openFor = $this->currentOpenSeconds();
            if (($this->openedAt + $openFor) > $now) {
                return $this->fallback('circuit_open');
            }
            // окно прошло → half-open
            $this->state = 2;
            $this->probeTaken = false;
        }

        if ($this->state === 2) { // half-open — один пробный вызов
            if ($this->probeTaken) {
                return $this->fallback('half_open_backoff');
            }
            $this->probeTaken = true;
            try {
                $res = $this->inner->check($s, $a, $sc, $context);
                $this->reset();
                return $res;
            } catch (Throwable $e) {
                $this->reopen();
                return $this->fallback('probe_failed');
            }
        }

        // closed
        try {
            $res = $this->inner->check($s, $a, $sc, $context);
            $this->reset();
            return $res;
        } catch (Throwable $e) {
            if ($e instanceof RemoteHttpException) {
                if ($e->status() >= 500) {
                    $this->failures++;
                } else {
                    // 4xx не считаем как сбой подсистемы
                    throw $e;
                }
            } else {
                // сетевые/прочие ошибки считаем как сбой
                $this->failures++;
            }

            if ($this->failures >= $this->failureThreshold) {
                $this->open();
                return $this->fallback('opened_after_failures');
            }
            // Пока не достигли порога — пробрасываем ошибку наверх
            throw $e;
        }
    }

    /**
     * @return void
     */
    private function open(): void
    {
        $this->state = 1; // open
        $this->openedAt = $this->clock->now();
        $this->retries++;
    }

    /**
     * @return void
     */
    private function reopen(): void
    {
        $this->state = 1;
        $this->openedAt = $this->clock->now();
        $this->retries++;
        $this->probeTaken = false;
    }

    /**
     * @return void
     */
    private function reset(): void
    {
        $this->state = 0;
        $this->failures = 0;
        $this->retries = 0;
        $this->openedAt = 0;
        $this->probeTaken = false;
    }

    /**
     * @return int
     */
    private function currentOpenSeconds(): int
    {
        $sec = (int) ($this->openBaseSeconds * (2 ** max(0, $this->retries - 1)));
        return min($sec, $this->openMaxSeconds);
    }

    /**
     * @param string $reason
     * @return \Policy\Role\V2\DecisionWithObligations
     */
    private function fallback(string $reason): DecisionWithObligations
    {
        $obs = (Obligations::empty())->with(new Obligation('degraded', ['reason' => $reason, 'breaker' => $this->id]));
        return DecisionWithObligations::deny('circuit_breaker', $obs);
    }
}
