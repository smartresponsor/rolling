#!/usr/bin/env php
<?php
declare(strict_types=1);
/** Append a tuple-change event to var/tuples.ndjson */
$path = $argv[1] ?? __DIR__ . '/../var/tuples.ndjson';
$tenant = $argv[2] ?? 't1';
$subject = $argv[3] ?? 'user:123';
$relation = $argv[4] ?? 'viewer';
$resource = $argv[5] ?? 'doc:42';
$ev = ['ts' => gmdate('c'), 'tenant' => $tenant, 'subject' => $subject, 'relation' => $relation, 'resource' => $resource, 'op' => 'upsert'];
file_put_contents($path, json_encode($ev, JSON_UNESCAPED_SLASHES) . "\n", FILE_APPEND);
echo "ok\n";
