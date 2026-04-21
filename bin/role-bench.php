#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Автономный бенчмаркер. Вызывает набор сценариев и пишет результаты в report/bench.
 */

use App\Rolling\Policy\Batch\CheckBatchProcessor;
use App\Rolling\Policy\Obligation\Obligations;
use App\Rolling\Policy\V2\DecisionWithObligations;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;
use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;

const DEFAULT_ITER = 20000;
const DEFAULT_BATCH_N = 3000;

$root = getcwd();
$reportDir = $root . '/report/bench';
@mkdir($reportDir, 0777, true);

/**
 * @return float
 */
function hr(): float
{
    return hrtime(true) / 1_000_000_000.0;
}

/**
 * @param array $arr
 * @param float $p
 * @return float
 */
function pct(array $arr, float $p): float
{
    sort($arr);
    $idx = (int)max(0, min(count($arr) - 1, floor($p * (count($arr) - 1))));
    return $arr[$idx];
}

/**
 * @param string $path
 * @param array $data
 * @return void
 */
function write_json(string $path, array $data): void
{
    file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
}

/**
 * @param string $path
 * @param array $rows
 * @param array|null $header
 * @return void
 */
function write_csv(string $path, array $rows, ?array $header = null): void
{
    $fh = fopen($path, 'w');
    if ($fh === false) {
        return;
    }

    if ($header !== null) {
        fputcsv($fh, $header);
    }

    foreach ($rows as $row) {
        fputcsv($fh, $row);
    }
    fclose($fh);
}

/** serial_ctx: нормализация+json */
function bench_serial_ctx(int $n = DEFAULT_ITER): array
{
    $samples = [];
    $ctx = ['z' => 3, 'a' => ['k' => 2, 'b' => 1], 'm' => 'str', 'num' => 42];
    $norm = function (array $a) use (&$norm): array {
        ksort($a);
        foreach ($a as $k => $v) if (is_array($v)) $a[$k] = $norm($v);
        return $a;
    };
    for ($i = 0; $i < $n; $i++) {
        $t0 = hr();
        $h = hash('sha256', json_encode($norm($ctx), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $t1 = hr();
        $samples[] = ($t1 - $t0) * 1000.0; // ms
        $ctx['num'] = ($ctx['num'] + 1) % 1000;
    }
    return ['name' => 'serial_ctx', 'n' => $n, 'samples_ms' => $samples];
}

/** cache_hit: модель PDP кеша */
final class SimpleCache
{
    private array $m = [];

    /**
     * @param string $k
     * @return mixed
     */
    public function get(string $k): mixed
    {
        return $this->m[$k] ?? null;
    }

    /**
     * @param string $k
     * @param mixed $v
     * @return void
     */
    public function set(string $k, mixed $v): void
    {
        $this->m[$k] = $v;
    }
}

/**
 * @param int $n
 * @return array
 */
function bench_cache_hit(int $n = DEFAULT_ITER): array
{
    $samples = [];
    $cache = new SimpleCache();
    $key = 'v2:u:global:a:ctx:abc:se:0';
    $cache->set($key, ['allow' => true, 'reason' => 'ok']);
    for ($i = 0; $i < $n; $i++) {
        $t0 = hr();
        $v = $cache->get($key);
        $t1 = hr();
        $samples[] = ($t1 - $t0) * 1000.0;
        if (!$v) throw new RuntimeException('cache miss in cache_hit bench');
    }
    return ['name' => 'cache_hit', 'n' => $n, 'samples_ms' => $samples];
}

/** rpc_sim: имитация задержки сети */
function bench_rpc_sim(int $n = 2000, int $us = 200): array
{
    $samples = [];
    for ($i = 0; $i < $n; $i++) {
        $t0 = hr();
        if ($us > 0) usleep($us);
        $t1 = hr();
        $samples[] = ($t1 - $t0) * 1000.0;
    }
    return ['name' => 'rpc_sim', 'n' => $n, 'param_us' => $us, 'samples_ms' => $samples];
}

/** batch_proc: если доступен CheckBatchProcessor из RC-B3 */
function bench_batch_proc(int $n = DEFAULT_BATCH_N, int $chunk = 128): ?array
{
    if (!class_exists(CheckBatchProcessor::class)) {
        return null;
    }
    // Дамми PDP
    $inner = new class implements PdpV2Interface {
        /**
         * @param \App\Rolling\Entity\Role\SubjectId $s
         * @param \App\Rolling\Entity\Role\PermissionKey $a
         * @param \App\Rolling\Entity\Role\Scope $sc
         * @param array $ctx
         * @return \App\Rolling\Policy\V2\DecisionWithObligations
         */
        public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $ctx = []): DecisionWithObligations
        {
            return DecisionWithObligations::allow('ok', Obligations::empty());
        }
    };
    $proc = new CheckBatchProcessor($inner);
    $reqs = [];
    for ($i = 0; $i < $n; $i++) {
        $reqs[] = ['subjectId' => "u$i", 'action' => 'a', 'scopeType' => 'global'];
    }
    $t0 = hr();
    $count = 0;
    foreach ($proc->process($reqs, ['chunkSize' => $chunk]) as $row) {
        $count++;
    }
    $t1 = hr();
    $durMs = ($t1 - $t0) * 1000.0;
    return ['name' => 'batch_proc', 'n' => $n, 'chunk' => $chunk, 'duration_ms' => $durMs, 'per_item_ms' => $durMs / $n];
}

// ---- Run ----
$scenarios = [];
$scenarios[] = bench_serial_ctx();
$scenarios[] = bench_cache_hit();
$scenarios[] = bench_rpc_sim();
$bp = bench_batch_proc();
if ($bp) $scenarios[] = $bp;

// Write raw
$ts = date('Ymd_His');
$rawPath = $reportDir . "/raw_$ts.json";
write_json($rawPath, ['scenarios' => $scenarios]);

// Flatten summary
$rows = [];
foreach ($scenarios as $s) {
    if (isset($s['samples_ms'])) {
        $p50 = pct($s['samples_ms'], 0.50);
        $p95 = pct($s['samples_ms'], 0.95);
        $p99 = pct($s['samples_ms'], 0.99);
        $rows[] = [$s['name'], $s['n'], round($p50, 4), round($p95, 4), round($p99, 4), '—', '—'];
    } else {
        $rows[] = [$s['name'], $s['n'] ?? '—', '—', '—', '—', round($s['duration_ms'], 3) ?? '—', round(($s['per_item_ms'] ?? 0.0), 3)];
    }
}
$csvPath = $reportDir . "/summary_$ts.csv";
write_csv($csvPath, $rows, ['scenario', 'n', 'p50_ms', 'p95_ms', 'p99_ms', 'duration_ms', 'per_item_ms']);

// Build markdown
$md = "# Benchmark report ($ts)\n\n";
$md .= "| scenario | n | p50 ms | p95 ms | p99 ms | duration ms | per item ms |\n";
$md .= "|---|---:|---:|---:|---:|---:|---:|\n";
foreach ($rows as $r) {
    $md .= "| {$r[0]} | {$r[1]} | {$r[2]} | {$r[3]} | {$r[4]} | {$r[5]} | {$r[6]} |\n";
}
$md .= "\n> Примечание: `rpc_sim` использует usleep, `batch_proc` — потоковый процессор из RC-B3 (если доступен).\n";
file_put_contents($root . '/docs/benchmark.md', $md);

echo "OK. Wrote:\n- $rawPath\n- $csvPath\n- docs/benchmark.md\n";

