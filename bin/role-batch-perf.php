#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Rolling\Policy\Batch\CheckBatchProcessor;
use App\Rolling\Policy\Obligation\Obligations;
use App\Rolling\Policy\V2\DecisionWithObligations;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\SubjectId;

require __DIR__ . '/../vendor/autoload.php';

[$script, $nStr, $sleepUsStr] = $argv + [null, '1000', '0'];
$n = (int)$nStr;
$sleepUs = (int)$sleepUsStr;

// Dummy PDP with optional micro-sleep (симулирует сетевую или вычисл. задержку)
$inner = new class($sleepUs) implements PdpV2Interface {
    /**
     * @param int $us
     */
    public function __construct(private readonly int $us)
    {
    }

    /**
     * @param \App\Rolling\Entity\Role\SubjectId $s
     * @param \App\Rolling\Entity\Role\PermissionKey $a
     * @param \App\Rolling\Entity\Role\Scope $sc
     * @param array $ctx
     * @return \App\Rolling\Policy\V2\DecisionWithObligations
     */
    public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $ctx = []): DecisionWithObligations
    {
        if ($this->us > 0) usleep($this->us);
        return DecisionWithObligations::allow('ok', Obligations::empty());
    }
};
$proc = new CheckBatchProcessor($inner);

// Prepare requests
$reqs = [];
for ($i = 0; $i < $n; $i++) {
    $reqs[] = ['subjectId' => "u$i", 'action' => 'message.read', 'scopeType' => 'global', 'context' => ['i' => $i]];
}

$start = hrtime(true);
$results = 0;
foreach ($proc->process($reqs, ['chunkSize' => 128, 'maxItems' => $n]) as $row) {
    $results++;
}
$end = hrtime(true);
$durMs = ($end - $start) / 1_000_000.0;
$perItem = $results ? ($durMs / $results) : 0.0;

echo json_encode([
        'n' => $n,
        'sleep_us' => $sleepUs,
        'duration_ms' => round($durMs, 3),
        'per_item_ms' => round($perItem, 3),
        'peak_mb' => round(memory_get_peak_usage(true) / 1048576, 2),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
