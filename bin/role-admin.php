#!/usr/bin/env php
<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use App\Service\Admin\RebacStatsService;
use App\Infrastructure\Rebac\{InMemoryTupleStore, PdoTupleStore};
use App\Legacy\Policy\Registry\{InMemoryRegistryStore};
use App\Legacy\Policy\Registry\PdoRegistryStore;
use App\Legacy\Policy\Registry\RegistryService;

$ns = getenv('ROLE_ADMIN_NS') ?: 'default';
$tok = getenv('ROLE_ADMIN_TOKEN') ?: null;
$policyDsn = getenv('ROLE_POLICY_DSN') ?: null;
$rebacDsn = getenv('ROLE_REBAC_DSN') ?: null;

$policyStore = $policyDsn ? new PdoRegistryStore(new PDO($policyDsn)) : new InMemoryRegistryStore();
$rebacStore = $rebacDsn ? new PdoTupleStore(new PDO($rebacDsn)) : new InMemoryTupleStore();

$policySvc = new RegistryService($policyStore);
$rebacStats = new RebacStatsService($rebacStore);

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
    case 'policy:import':
        // role-admin policy:import <name> <version> <file.json>
        [$name, $ver, $file] = array_slice($argv, 2) + [null, null, null];
        $doc = readFileStrict((string)$file);
        $policySvc->importPolicy($ns, (string)$name, (string)$ver, $doc);
        echo "imported $ns/$name@$ver\n";
        break;
    case 'policy:activate':
        // role-admin policy:activate <name> <version>
        [$name, $ver] = array_slice($argv, 2) + [null, null];
        $policySvc->activatePolicy($ns, (string)$name, (string)$ver);
        echo "activated $ns/$name@$ver\n";
        break;
    case 'policy:list':
        // role-admin policy:list <name>
        [$name] = array_slice($argv, 2) + [null];
        foreach ($policySvc->listVersions($ns, (string)$name) as $rec) {
            $mark = $rec->isActive ? '*' : ' ';
            echo sprintf("%s %s/%s@%s (ts=%d)\n", $mark, $rec->ns, $rec->name, $rec->version, $rec->createdAt);
        }
        break;
    case 'policy:export':
        // role-admin policy:export <name> <version> [out.json]
        [$name, $ver, $out] = array_slice($argv, 2) + [null, null, null];
        $doc = $policySvc->exportPolicy($ns, (string)$name, (string)$ver);
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
    case 'rebac:stats':
        // role-admin rebac:stats
        $s = $rebacStats->stats($ns);
        echo json_encode($s, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        break;
    default:
        echo "Usage:\n";
        echo "  ROLE_POLICY_DSN, ROLE_REBAC_DSN, ROLE_ADMIN_NS envs\n";
        echo "  policy:import <name> <version> <file.json>\n";
        echo "  policy:activate <name> <version>\n";
        echo "  policy:list <name>\n";
        echo "  policy:export <name> <version> [out.json]\n";
        echo "  rebac:stats\n";
        exit(1);
}
