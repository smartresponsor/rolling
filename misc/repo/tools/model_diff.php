#!/usr/bin/env php
<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/Service/Role/Model/Diff.php';
require_once __DIR__ . '/../src/Service/Role/Model/Validation.php';

use App\Service\Model\Diff;

[$_, $fromPath, $toPath] = $argv + [null, null, null];
if (!$fromPath || !$toPath) {
    fwrite(STDERR, "Usage: php tools/model_diff.php from.json to.json\n");
    exit(2);
}
$from = json_decode(file_get_contents($fromPath), true);
$to = json_decode(file_get_contents($toPath), true);
$res = Diff::compute($from, $to);
echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
