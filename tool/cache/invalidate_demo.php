#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Service\Cache\TagInvalidator;
use App\Service\Cache\PdpCache;

require_once __DIR__ . '/../../src/Service/Cache/TagInvalidator.php';
require_once __DIR__ . '/../../src/Service/Cache/StampedeGuard.php';
require_once __DIR__ . '/../../src/Service/Cache/PdpCache.php';

@mkdir(__DIR__ . '/../../report', 0775, true);

$tags = new TagInvalidator();
$cache = new PdpCache($tags);

$key = ['pdp' => 'v3', 'subject' => ['id' => 'u1', 'roles' => ['reader']], 'action' => 'read', 'resource' => ['type' => 'doc', 'id' => 'd1']];
$tagsList = ['policy:v2'];

// producer returns expensive value (timestamp + random)
$producer = function () {
    usleep(100 * 1000); // simulate heavy calc 100ms
    return ['ts' => date('c'), 'rand' => random_int(1000, 9999)];
};

$v1 = $cache->get($key, 1000, $tagsList, $producer);
$v2 = $cache->get($key, 1000, $tagsList, $producer); // should be cached

// invalidate policy
$tags->invalidateTags(['policy:v2']);
$v3 = $cache->get($key, 1000, $tagsList, $producer); // recomputed

$out = [
    'ts' => date('c'),
    'first' => $v1,
    'second' => $v2,
    'after_invalidate' => $v3,
    'same_before_invalidate' => ($v1 == $v2),
    'changed_after_invalidate' => ($v2 != $v3),
];
file_put_contents(__DIR__ . '/../../report/cache_invalidate_demo.json', json_encode($out, JSON_PRETTY_PRINT));
echo "report/cache_invalidate_demo.json written\n";
