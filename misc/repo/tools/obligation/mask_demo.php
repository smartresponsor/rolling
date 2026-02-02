#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infra\Role\Policy\Masking\InMemoryMaskingRuleRepository;
use App\Service\Role\Policy\Obligation\Masking\MaskingEngine;

require_once __DIR__ . '/../../src/Infra/Role/Policy/Masking/InMemoryMaskingRuleRepository.php';
require_once __DIR__ . '/../../src/Service/Role/Policy/Obligation/Masking/MaskingEngine.php';

$reportDir = __DIR__ . '/../../report';
@mkdir($reportDir, 0775, true);

$repo = new InMemoryMaskingRuleRepository();
$repo->loadFromNdjson(__DIR__ . '/../../examples/masking_rules.ndjson');
$engine = new MaskingEngine($repo);

$subject = ['id' => 'u9', 'roles' => ['support'], 'tenant' => 't1'];
$resource = ['type' => 'user', 'id' => 'U-1', 'tenant' => 't1', 'email' => 'user@example.com', 'fullName' => 'John Smith', 'ssn' => '123-45-6789'];
$ctx = ['tenant' => 't1'];

$result = $engine->apply($subject, 'can_read', $resource, $ctx);
file_put_contents($reportDir . '/mask_demo.json', json_encode($result, JSON_PRETTY_PRINT));
echo "mask_demo.json written\n";
