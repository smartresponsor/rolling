#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Service\Pdp\BatchDecision;
use App\Service\Pdp\Dto\DecisionRequest;

require_once __DIR__ . '/../../src/Service/Role/Pdp/BatchDecision.php';
require_once __DIR__ . '/../../src/Service/Role/Pdp/Dto/DecisionRequest.php';
require_once __DIR__ . '/../../src/Service/Role/Pdp/Dto/DecisionResponse.php';

/**
 * Read NDJSON of DecisionRequest-like objects from STDIN.
 * Emit NDJSON of DecisionResponse.
 */
$engine = new BatchDecision();

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') continue;
    $d = json_decode($line, true);
    if (!is_array($d)) {
        fwrite(STDERR, "bad json\n");
        continue;
    }
    $req = new DecisionRequest(
        $d['subject'] ?? [],
        (string)($d['action'] ?? 'read'),
        $d['resource'] ?? [],
        $d['context'] ?? []
    );
    $res = $engine->decideMany([$req])[0];
    echo json_encode($res->toArray(), JSON_UNESCAPED_SLASHES) . "\n";
    @ob_flush();
    @flush();
}
