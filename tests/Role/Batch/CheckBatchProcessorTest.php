<?php
declare(strict_types=1);

namespace Tests\Role\Batch;

use PHPUnit\Framework\TestCase;
use App\Legacy\Policy\Batch\CheckBatchProcessor;
use App\Policy\Obligation\Obligations;
use App\Policy\V2\DecisionWithObligations;
use App\PolicyInterface\PdpV2Interface;
use RuntimeException;
use App\Entity\Role\Scope;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class CheckBatchProcessorTest extends TestCase
{
    /**
     * @return void
     */
    public function testPartialSuccessAndIndices(): void
    {
        $inner = new class implements PdpV2Interface {
            /**
             * @param \App\Entity\Role\SubjectId $s
             * @param \App\Entity\Role\PermissionKey $a
             * @param \App\Entity\Role\Scope $sc
             * @param array $ctx
             * @return \Policy\Role\V2\DecisionWithObligations
             */
            public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $ctx = []): DecisionWithObligations
            {
                if (((int)($ctx['i'] ?? -1)) % 10 === 0) {
                    throw new RuntimeException('boom');
                }
                return DecisionWithObligations::allow('ok', Obligations::empty());
            }
        };
        $proc = new CheckBatchProcessor($inner);
        $reqs = [];
        for ($i = 0; $i < 100; $i++) {
            $reqs[] = ['subjectId' => "u$i", 'action' => 'a', 'scopeType' => 'global', 'context' => ['i' => $i]];
        }

        $ok = 0;
        $fail = 0;
        $seen = [];
        foreach ($proc->process($reqs, ['chunkSize' => 16]) as $row) {
            $seen[] = $row['idx'];
            if ($row['ok']) $ok++; else $fail++;
        }
        sort($seen);
        $this->assertSame(range(0, 99), $seen);
        $this->assertSame(90, $ok);
        $this->assertSame(10, $fail);
    }

    /**
     * @return void
     */
    public function testMemoryStability(): void
    {
        $inner = new class implements PdpV2Interface {
            /**
             * @param \App\Entity\Role\SubjectId $s
             * @param \App\Entity\Role\PermissionKey $a
             * @param \App\Entity\Role\Scope $sc
             * @param array $ctx
             * @return \Policy\Role\V2\DecisionWithObligations
             */
            public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $ctx = []): DecisionWithObligations
            {
                return DecisionWithObligations::allow('ok', Obligations::empty());
            }
        };
        $proc = new CheckBatchProcessor($inner);
        $reqs = [];
        for ($i = 0; $i < 5000; $i++) {
            $reqs[] = ['subjectId' => "u$i", 'action' => 'a', 'scopeType' => 'global'];
        }
        $start = memory_get_usage(true);
        $count = 0;
        foreach ($proc->process($reqs, ['chunkSize' => 128]) as $row) {
            $count++;
        }
        $peak = memory_get_peak_usage(true);
        $this->assertSame(5000, $count);
        $this->assertLessThan(256 * 1024 * 1024, $peak, 'peak memory must be below 256MB');
    }
}
