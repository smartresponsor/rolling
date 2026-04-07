#!/usr/bin/env php
<?php
declare(strict_types=1);
require __DIR__ . '/../src/Service/Explain/TupleReader.php';
require __DIR__ . '/../src/Service/Audit/Logger.php';
require __DIR__ . '/../src/Http/Role/Api/Consistency.php';
require __DIR__ . '/../src/Http/Role/Api/CheckController.php';

use App\Legacy\Http\Api\CheckController;
use Symfony\Component\HttpFoundation\Request;

[$_, $subject, $relation, $resource, $tenant] = $argv + [null, null, null, null, 't1'];
if (!$subject || !$relation || !$resource) {
    fwrite(STDERR, "Usage: php tools/check_cli.php <subject> <relation> <resource> [tenant=t1]\n");
    exit(2);
}
$payload = json_encode(['tenant' => $tenant, 'subject' => $subject, 'relation' => $relation, 'resource' => $resource]);
$req = new Request([], [], [], [], [], [], $payload);
$ctl = new CheckController(__DIR__ . '/../var/tuples.ndjson', __DIR__ . '/../var/log/role');
$res = $ctl->check($req);
echo $res->getContent(), PHP_EOL;
