<?php

declare(strict_types=1);

namespace Tests\Role\Resilience;

use App\Net\Http\RemoteHttpException;
use App\Service\Resilience\CircuitBreakingPdpV2;
use App\ServiceInterface\Resilience\Time\ClockInterface;
use PHPUnit\Framework\TestCase;
use App\Policy\Obligation\Obligations;
use App\Policy\V2\DecisionWithObligations;
use App\ServiceInterface\Policy\PdpV2Interface;
use App\Entity\Role\Scope;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\SubjectId;
use Throwable;

/**
 *
 */

/**
 *
 */
final class CircuitBreakingPdpV2Test extends TestCase
{
    /**
     * @return \App\Entity\Role\SubjectId
     */
    private function sid(): SubjectId
    {
        return new SubjectId('u1');
    }

    /**
     * @return \App\Entity\Role\PermissionKey
     */
    private function act(): PermissionKey
    {
        return new PermissionKey('read');
    }

    /**
     * @return \App\Entity\Role\Scope
     */
    private function sc(): Scope
    {
        return Scope::global();
    }

    /**
     * @return void
     */
    public function testOpenAndHalfOpenFlow(): void
    {
        // fake clock
        $now = 1_700_000_000;
        $clock = new class ($now) implements ClockInterface {
            /**
             * @param int $t
             */
            public function __construct(private int $t) {}

            /**
             * @return int
             */
            public function nowMs(): int
            {
                return $this->t * 1000;
            }

            /**
             * @param int $sec
             * @return void
             */
            public function tick(int $sec): void
            {
                $this->t += $sec;
            }
        };

        // inner fails with RemoteHttpException(500) first two times, then success
        $calls = 0;
        $inner = new class ($calls) implements PdpV2Interface {
            /**
             * @param int $calls
             */
            public function __construct(private int &$calls) {}

            /**
             * @param \App\Entity\Role\SubjectId $s
             * @param \App\Entity\Role\PermissionKey $a
             * @param \App\Entity\Role\Scope $sc
             * @param array $ctx
             * @return \Policy\Role\V2\DecisionWithObligations
             */
            public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $ctx = []): DecisionWithObligations
            {
                $this->calls++;
                if ($this->calls <= 2) {
                    throw new RemoteHttpException(500, 'boom');
                }
                return DecisionWithObligations::allow('ok', Obligations::empty());
            }
        };

        $breaker = new CircuitBreakingPdpV2($inner, 'test', failureThreshold: 2, openBaseSeconds: 10, openMaxSeconds: 60, clock: $clock);

        // 1st call -> exception bubbles (threshold not reached)
        try {
            $breaker->check($this->sid(), $this->act(), $this->sc());
            $this->fail('Expected exception');
        } catch (Throwable $e) {
        }

        // 2nd call -> reaches threshold -> opens and returns fallback
        try {
            $res2 = $breaker->check($this->sid(), $this->act(), $this->sc());
        } catch (Throwable $e) {
        }
        $this->assertFalse($res2->isAllow(), 'fallback deny');
        $this->assertStringContainsString('circuit_breaker', $res2->reason);

        // still open before window passes
        try {
            $res3 = $breaker->check($this->sid(), $this->act(), $this->sc());
        } catch (Throwable $e) {
        }
        $this->assertFalse($res3->isAllow(), 'still open');

        // advance time to allow half-open probe
        $clock->tick(10);
        try {
            $res4 = $breaker->check($this->sid(), $this->act(), $this->sc());
        } catch (Throwable $e) {
        }
        $this->assertTrue($res4->isAllow(), 'half-open probe success closes breaker');

        // now closed -> normal success
        try {
            $res5 = $breaker->check($this->sid(), $this->act(), $this->sc());
        } catch (Throwable $e) {
        }
        $this->assertTrue($res5->isAllow());
    }
}
