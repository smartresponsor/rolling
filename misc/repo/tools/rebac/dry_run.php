#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infrastructure\Rebac\NullRebacClient;
use App\Service\Pdp\Policy\TupleMapper;

require_once __DIR__ . '/../../src/Service/Role/Pdp/Policy/TupleMapper.php';
require_once __DIR__ . '/../../src/Infra/Role/Rebac/Tuple.php';
require_once __DIR__ . '/../../src/Infra/Role/Rebac/NullRebacClient.php';

$input = __DIR__ . '/../../examples/grants.ndjson';
$reportDir = __DIR__ . '/../../report';
@mkdir($reportDir, 0775, true);

$grants = [];
$fh = fopen($input, 'r');
while (($line = fgets($fh)) !== false) {
    $grants[] = json_decode($line, true);
}
fclose($fh);

$tuples = TupleMapper::toTuples($grants);
$client = new NullRebacClient();
$client->writeTuples($tuples);

$plan = [
    'tuples_count' => count($tuples),
    'example' => $tuples ? $tuples[0]->toArray() : null,
    'backend' => $client->health(),
];
file_put_contents($reportDir . '/rebac_dry_run.json', json_encode($plan, JSON_PRETTY_PRINT));
echo "Dry-run report: report/rebac_dry_run.json\n";
