#!/usr/bin/env php
<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/Service/Explain/TupleReader.php';
require_once __DIR__ . '/../src/Service/Explain/Planner.php';
require_once __DIR__ . '/../src/Service/Explain/Renderer.php';

use App\Service\Explain\{Planner};
use App\Service\Explain\Renderer;
use App\Service\Explain\TupleReader;

[$_, $subject, $relation, $resource, $tenant] = $argv + [null, null, null, null, 't1'];
if (!$subject || !$relation || !$resource) {
    fwrite(STDERR, "Usage: php tools/explain_cli.php <subject> <relation> <resource> [tenant=t1]\n");
    exit(2);
}
$plan = (new Planner(new TupleReader()))->plan($tenant, $subject, $relation, $resource);
echo json_encode($plan, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
echo "\nDOT:\n";
echo Renderer::toDot($plan['nodes'], $plan['edges']), PHP_EOL;
