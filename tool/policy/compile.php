#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Service\Policy\PelCompiler;

require_once __DIR__ . '/../../src/Service/Role/Policy/PelCompiler.php';
require_once __DIR__ . '/../../src/ServiceInterface/Role/Policy/CompilerInterface.php';

$name = $argv[1] ?? 'policy_v1';
$input = $argv[2] ?? __DIR__ . '/../../policy/policy_v1.pel.json';
$compiler = new PelCompiler();
$out = $compiler->compile($name, $input, __DIR__ . '/../../var/policy_compiled');
echo $out, "\n";
