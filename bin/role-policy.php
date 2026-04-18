#!/usr/bin/env php
<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use App\Legacy\Policy\Registry\{InMemoryRegistryStore};
use App\Legacy\Policy\Registry\PdoRegistryStore;
use App\Legacy\Policy\Registry\RegistryService;

$ns = getenv('ROLE_POLICY_NS') ?: 'default';
$dsn = getenv('ROLE_POLICY_DSN') ?: null;

$store = $dsn ? new PdoRegistryStore(new PDO($dsn)) : new InMemoryRegistryStore();
$svc = new RegistryService($store);

$argv = $_SERVER['argv'];
$cmd = $argv[1] ?? 'help';

/**
 * @param string $path
 * @return string
 */
function readFileStrict(string $path): string
{
    $s = @file_get_contents($path);
    if ($s === false) {
        throw new RuntimeException("cannot read $path");
    }
    return $s;
}

switch ($cmd) {
    case 'import':
        // role-policy import <name> <version> <file.json>
        [$name, $ver, $file] = array_slice($argv, 2) + [null, null, null];
        $doc = readFileStrict((string)$file);
        $svc->importPolicy($ns, (string)$name, (string)$ver, $doc);
        echo "imported $ns/$name@$ver\n";
        break;
    case 'activate':
        // role-policy activate <name> <version>
        [$name, $ver] = array_slice($argv, 2) + [null, null];
        $svc->activatePolicy($ns, (string)$name, (string)$ver);
        echo "active $ns/$name@$ver\n";
        break;
    case 'export':
        // role-policy export <name> <version> [out.json]
        [$name, $ver, $out] = array_slice($argv, 2) + [null, null, null];
        $doc = $svc->exportPolicy($ns, (string)$name, (string)$ver);
        if ($doc === null) {
            fwrite(STDERR, "not found\n");
            exit(2);
        }
        if ($out) {
            file_put_contents((string)$out, $doc);
            echo "wrote $out\n";
        } else {
            echo $doc . PHP_EOL;
        }
        break;
    case 'list':
        // role-policy list <name>
        [$name] = array_slice($argv, 2) + [null];
        foreach ($svc->listVersions($ns, (string)$name) as $rec) {
            $mark = $rec->isActive ? '*' : ' ';
            echo sprintf("%s %s/%s@%s (ts=%d)\n", $mark, $rec->ns, $rec->name, $rec->version, $rec->createdAt);
        }
        break;
    case 'migrate':
        // role-policy migrate <name> <from> <to> [note]
        [$name, $from, $to, $note] = array_slice($argv, 2) + [null, null, null, null];
        $svc->recordMigration($ns, (string)$name, (string)$from, (string)$to, $note ? (string)$note : null);
        $svc->activatePolicy($ns, (string)$name, (string)$to);
        echo "migrated $ns/$name: $from -> $to\n";
        break;
    default:
        echo "Usage:\n";
        echo "  ROLE_POLICY_DSN='sqlite:./var/policy.db' ROLE_POLICY_NS='acme' php bin/role-policy.php import <name> <version> <file.json>\n";
        echo "  php bin/role-policy.php activate <name> <version>\n";
        echo "  php bin/role-policy.php export <name> <version> [out.json]\n";
        echo "  php bin/role-policy.php list <name>\n";
        echo "  php bin/role-policy.php migrate <name> <from> <to> [note]\n";
        exit(1);
}
