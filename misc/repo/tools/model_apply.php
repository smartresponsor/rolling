#!/usr/bin/env php
<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/Service/Role/Model/SchemaStorageInterface.php';
require_once __DIR__ . '/../src/Service/Role/Model/FileSchemaStorage.php';
require_once __DIR__ . '/../src/Service/Role/Model/SchemaRegistry.php';
require_once __DIR__ . '/../src/Service/Role/Model/Diff.php';
require_once __DIR__ . '/../src/Service/Role/Model/Migrator.php';

use App\Service\Model\Migrator;
use App\Service\Model\FileSchemaStorage;
use App\Service\Model\SchemaRegistry;

[$_, $version, $schemaPath, $dry] = $argv + [null, null, null, "0"];
if (!$version || !$schemaPath) {
    fwrite(STDERR, "Usage: php tools/model_apply.php <version> <schema.json> [dry=0|1]\n");
    exit(2);
}
$storage = new FileSchemaStorage(__DIR__ . '/../var/role_schema.json');
$registry = new SchemaRegistry($storage);
$migrator = new Migrator($registry);

$schema = json_decode(file_get_contents($schemaPath), true);
$res = $migrator->apply($version, $schema, $dry === "1");
echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
