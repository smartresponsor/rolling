#!/usr/bin/env php
<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use App\Legacy\Model\Rebac\Tuple;
use App\Service\Rebac\{Checker, Writer};
use App\Infrastructure\Rebac\{InMemoryTupleStore, PdoTupleStore};

$dsn = getenv('ROLE_REBAC_DSN') ?: null;
$ns = getenv('ROLE_REBAC_NS') ?: 'default';

if ($dsn) {
    $pdo = new PDO($dsn);
    $store = new PdoTupleStore($pdo);
} else {
    $store = new InMemoryTupleStore();
}
$writer = new Writer($store);
$checker = new Checker($store);

$cmd = $argv[1] ?? 'help';
switch ($cmd) {
    case 'write':
        // php bin/role-rebac.php write doc 1 viewer user 42
        [$objType, $objId, $rel, $subjType, $subjId] = array_slice($argv, 2) + [null, null, null, null, null];
        $rev = $writer->write($ns, [new Tuple($ns, (string)$objType, (string)$objId, (string)$rel, (string)$subjType, (string)$subjId, null)]);
        echo "rev=$rev\n";
        break;
    case 'check':
        // php bin/role-rebac.php check user:42 doc:1 viewer
        [$subject, $object, $rel] = array_slice($argv, 2) + [null, null, null];
        $res = $checker->check($ns, (string)$subject, (string)$object, (string)$rel);
        echo json_encode($res, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        break;
    default:
        echo "Usage:\n";
        echo "  ROLE_REBAC_DSN='sqlite:/path.db' php bin/role-rebac.php write <objType> <objId> <relation> <subjType> <subjId>\n";
        echo "  php bin/role-rebac.php check <subject> <object> <relation>\n";
        echo "Env: ROLE_REBAC_DSN, ROLE_REBAC_NS\n";
}
