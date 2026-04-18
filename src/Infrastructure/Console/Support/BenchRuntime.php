<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Support;

use App\Policy\Batch\CheckBatchProcessor;
use App\Policy\Obligation\Obligations;
use App\Policy\V2\DecisionWithObligations;
use App\ServiceInterface\Policy\PdpV2Interface;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;

final class BenchRuntime
{
    /**
     * @return array<string, mixed>
     */
    public function run(int $iterations = 20000, int $batchN = 3000, int $rpcUs = 200): array
    {
        $scenarios = [
            $this->benchSerialContext($iterations),
            $this->benchCacheHit($iterations),
            $this->benchRpcSim(2000, $rpcUs),
        ];

        $batch = $this->benchBatchProc($batchN, 128);
        if ($batch !== null) {
            $scenarios[] = $batch;
        }

        return [
            'ok' => true,
            'iterations' => $iterations,
            'batch_n' => $batchN,
            'rpc_us' => $rpcUs,
            'scenarios' => $scenarios,
        ];
    }

    private function hr(): float
    {
        return hrtime(true) / 1_000_000_000.0;
    }

    /**
     * @param array<int, float|int> $arr
     */
    private function percentNormalise(array $arr): array
    {
        ksort($arr);
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $arr[$k] = $this->percentNormalise($v);
            }
        }

        return $arr;
    }

    /** @return array<string, mixed> */
    private function benchSerialContext(int $n): array
    {
        $samples = [];
        $ctx = ['z' => 3, 'a' => ['k' => 2, 'b' => 1], 'm' => 'str', 'num' => 42];
        for ($i = 0; $i < $n; ++$i) {
            $t0 = $this->hr();
            hash('sha256', json_encode($this->percentNormalise($ctx), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $t1 = $this->hr();
            $samples[] = ($t1 - $t0) * 1000.0;
            $ctx['num'] = ($ctx['num'] + 1) % 1000;
        }

        return ['name' => 'serial_ctx', 'n' => $n, 'samples_ms' => $samples];
    }

    /** @return array<string, mixed> */
    private function benchCacheHit(int $n): array
    {
        $samples = [];
        $cache = ['v2:u:global:a:ctx:abc:se:0' => ['allow' => true, 'reason' => 'ok']];
        $key = 'v2:u:global:a:ctx:abc:se:0';
        for ($i = 0; $i < $n; ++$i) {
            $t0 = $this->hr();
            $value = $cache[$key] ?? null;
            $t1 = $this->hr();
            if ($value === null) {
                throw new \RuntimeException('cache miss in cache_hit bench');
            }
            $samples[] = ($t1 - $t0) * 1000.0;
        }

        return ['name' => 'cache_hit', 'n' => $n, 'samples_ms' => $samples];
    }

    /** @return array<string, mixed> */
    private function benchRpcSim(int $n, int $us): array
    {
        $samples = [];
        for ($i = 0; $i < $n; ++$i) {
            $t0 = $this->hr();
            if ($us > 0) {
                usleep($us);
            }
            $t1 = $this->hr();
            $samples[] = ($t1 - $t0) * 1000.0;
        }

        return ['name' => 'rpc_sim', 'n' => $n, 'param_us' => $us, 'samples_ms' => $samples];
    }

    /** @return array<string, mixed>|null */
    private function benchBatchProc(int $n, int $chunk): ?array
    {
        if (!class_exists(CheckBatchProcessor::class)) {
            return null;
        }

        $inner = new class implements PdpV2Interface {
            public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $ctx = []): DecisionWithObligations
            {
                return DecisionWithObligations::allow('ok', Obligations::empty());
            }
        };

        $processor = new CheckBatchProcessor($inner);
        $requests = [];
        for ($i = 0; $i < $n; ++$i) {
            $requests[] = ['subjectId' => sprintf('u%d', $i), 'action' => 'a', 'scopeType' => 'global'];
        }

        $t0 = $this->hr();
        $count = 0;
        foreach ($processor->process($requests, ['chunkSize' => $chunk]) as $row) {
            ++$count;
        }
        $t1 = $this->hr();
        $durationMs = ($t1 - $t0) * 1000.0;

        return [
            'name' => 'batch_proc',
            'n' => $n,
            'chunk' => $chunk,
            'duration_ms' => round($durationMs, 3),
            'per_item_ms' => round($n > 0 ? $durationMs / $n : 0.0, 3),
        ];
    }
}
