#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Service\Role\Cache\TagInvalidator;
use App\Service\Role\Cache\PdpCache;

require_once __DIR__ . '/../../src/Service/Role/Cache/TagInvalidator.php';
require_once __DIR__ . '/../../src/Service/Role/Cache/StampedeGuard.php';
require_once __DIR__ . '/../../src/Service/Role/Cache/PdpCache.php';

@mkdir(__DIR__ . '/../../report', 0775, true);

$tags = new TagInvalidator();
$cache = new PdpCache($tags);
$key = ['heavy' => 'value'];
$tagsList = ['policy:v2'];

$producer = function () {
    usleep(150 * 1000);
    return ['ts' => date('c'), 'id' => 'heavy'];
};

$start = microtime(true);
$values = [];
for ($i = 0; $i < 5; $i++) {
    $values[] = $cache->get($key, 1500, $tagsList, $producer);
}
$elapsedMs = (microtime(true) - $start) * 1000;

$out = [
    'ts' => date('c'),
    'calls' => count($values),
    'elapsedMs' => $elapsedMs,
    'all_equal' => count(array_unique(array_map('json_encode', $values))) === 1,
];
file_put_contents(__DIR__ . '/../../report/cache_stampede_demo.json', json_encode($out, JSON_PRETTY_PRINT));
echo "report/cache_stampede_demo.json written\n";
