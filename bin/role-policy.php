#!/usr/bin/env php
<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use App\Rolling\Infrastructure\Policy\Registry\InMemoryStore;
use App\Rolling\Infrastructure\Policy\Registry\PdoStore;
use App\Rolling\Infrastructure\Policy\Registry\RegistryService;

$ns = getenv('ROLE_POLICY_NS') ?: 'default';
$dsn = getenv('ROLE_POLICY_DSN') ?: null;

$store = $dsn ? new PdoStore(new PDO($dsn)) : new InMemoryStore();
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
        // role-policy import <nameEntity> <version> <file.json>
        [$nameEntity, $ver, $file] = array_slice($argv, 2) + [null, null, null];
        $doc = readFileStrict((string)$file);
        $svc->importPolicy($ns, (string)$nameEntity, (string)$ver, $doc);
        echo "imported $ns/$nameEntity@$ver\n";
        break;
    case 'activate':
        // role-policy activate <nameEntity> <version>
        [$nameEntity, $ver] = array_slice($argv, 2) + [null, null];
        $svc->activatePolicy($ns, (string)$nameEntity, (string)$ver);
        echo "active $ns/$nameEntity@$ver\n";
        break;
    case 'export':
        // role-policy export <nameEntity> <version> [out.json]
        [$nameEntity, $ver, $out] = array_slice($argv, 2) + [null, null, null];
        $doc = $svc->exportPolicy($ns, (string)$nameEntity, (string)$ver);
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
        // role-policy list <nameEntity>
        [$nameEntity] = array_slice($argv, 2) + [null];
        foreach ($svc->listVersions($ns, (string)$nameEntity) as $rec) {
            $mark = $rec->isActive ? '*' : ' ';
            echo sprintf("%s %s/%s@%s (ts=%d)\n", $mark, $rec->ns, $rec->nameEntity, $rec->version, $rec->createdAt);
        }
        break;
    case 'migrate':
        // role-policy migrate <nameEntity> <from> <to> [note]
        [$nameEntity, $from, $to, $note] = array_slice($argv, 2) + [null, null, null, null];
        $svc->recordMigration($ns, (string)$nameEntity, (string)$from, (string)$to, $note ? (string)$note : null);
        $svc->activatePolicy($ns, (string)$nameEntity, (string)$to);
        echo "migrated $ns/$name: $from -> $to\n";
        break;
    default:
        echo "Usage:\n";
        echo "  ROLE_POLICY_DSN='sqlite:./var/policy.db' ROLE_POLICY_NS='acme' php bin/role-policy.php import <nameEntity> <version> <file.json>\n";
        echo "  php bin/role-policy.php activate <nameEntity> <version>\n";
        echo "  php bin/role-policy.php export <nameEntity> <version> [out.json]\n";
        echo "  php bin/role-policy.php list <nameEntity>\n";
        echo "  php bin/role-policy.php migrate <nameEntity> <from> <to> [note]\n";
        exit(1);
}

