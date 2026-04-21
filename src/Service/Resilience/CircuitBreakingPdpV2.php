<?php

declare(strict_types=1);

namespace App\Rolling\Service\Resilience;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Net\Http\RemoteHttpException;
use App\Rolling\Policy\Obligation\Obligation;
use App\Rolling\Policy\Obligation\Obligations;
use App\Rolling\Policy\V2\DecisionWithObligations;
use App\Rolling\Service\Resilience\Time\SystemClock;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;
use App\Rolling\ServiceInterface\Resilience\Time\ClockInterface;

final class CircuitBreakingPdpV2 implements PdpV2Interface
{
    private int $state = 0;
    private int $failures = 0;
    private int $openedAtMs = 0;
    private int $retries = 0;
    private bool $probeTaken = false;

    public function __construct(
        private readonly PdpV2Interface $inner,
        private readonly string $breakerId = 'role.pdp.v2',
        private readonly int $failureThreshold = 3,
        private readonly int $openBaseSeconds = 5,
        private readonly int $openMaxSeconds = 120,
        private readonly ClockInterface $clock = new SystemClock(),
    ) {
    }

    public function check(SubjectId $subject, PermissionKey $action, Scope $objectScope, array $context = []): DecisionWithObligations
    {
        $nowMs = $this->clock->nowMs();

        if (1 === $this->state) {
            $openForMs = $this->currentOpenMilliseconds();
            if (($this->openedAtMs + $openForMs) > $nowMs) {
                return $this->fallback('circuit_open');
            }

            $this->state = 2;
            $this->probeTaken = false;
        }

        if (2 === $this->state) {
            if ($this->probeTaken) {
                return $this->fallback('half_open_backoff');
            }

            $this->probeTaken = true;

            try {
                $decision = $this->inner->check($subject, $action, $objectScope, $context);
                $this->reset();

                return $decision;
            } catch (\Throwable $throwable) {
                $this->reopen();

                return $this->fallback('probe_failed');
            }
        }

        try {
            $decision = $this->inner->check($subject, $action, $objectScope, $context);
            $this->reset();

            return $decision;
        } catch (\Throwable $throwable) {
            if ($throwable instanceof RemoteHttpException) {
                if ($throwable->status() >= 500) {
                    ++$this->failures;
                } else {
                    throw $throwable;
                }
            } else {
                ++$this->failures;
            }

            if ($this->failures >= $this->failureThreshold) {
                $this->open();

                return $this->fallback('opened_after_failures');
            }

            throw $throwable;
        }
    }

    private function open(): void
    {
        $this->state = 1;
        $this->openedAtMs = $this->clock->nowMs();
        ++$this->retries;
    }

    private function reopen(): void
    {
        $this->state = 1;
        $this->openedAtMs = $this->clock->nowMs();
        ++$this->retries;
        $this->probeTaken = false;
    }

    private function reset(): void
    {
        $this->state = 0;
        $this->failures = 0;
        $this->retries = 0;
        $this->openedAtMs = 0;
        $this->probeTaken = false;
    }

    private function currentOpenMilliseconds(): int
    {
        $seconds = (int) ($this->openBaseSeconds * (2 ** max(0, $this->retries - 1)));

        return min($seconds, $this->openMaxSeconds) * 1000;
    }

    private function fallback(string $reason): DecisionWithObligations
    {
        $obligations = Obligations::empty()->with(new Obligation('degraded', [
            'reason' => $reason,
            'breaker' => $this->breakerId,
        ]));

        return DecisionWithObligations::deny('circuit_breaker', $obligations);
    }
}
