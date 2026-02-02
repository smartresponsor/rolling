#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infra\Role\Audit\FileAuditRepository;
use Audit\Dto\DecisionInput;
use Audit\Dto\DecisionRecord;
use Audit\Dto\DecisionResult;
use Audit\Explain\RuleExplainer;
use Audit\SimpleAuditLogger;

require_once __DIR__ . '/../../src/Infra/Role/Audit/FileAuditRepository.php';
require_once __DIR__ . '/../../src/Service/Role/Audit/SimpleAuditLogger.php';
require_once __DIR__ . '/../../src/Service/Role/Audit/Explain/RuleExplainer.php';
require_once __DIR__ . '/../../src/Service/Role/Audit/Dto/DecisionInput.php';
require_once __DIR__ . '/../../src/Service/Role/Audit/Dto/DecisionResult.php';
require_once __DIR__ . '/../../src/Service/Role/Audit/Dto/DecisionRecord.php';

$reportDir = __DIR__ . '/../../report';
@mkdir($reportDir, 0775, true);

$repo = new FileAuditRepository($reportDir . '/audit.ndjson');
$logger = new SimpleAuditLogger($repo);
$explainer = new RuleExplainer();

$input = new DecisionInput(
    ['id' => 'user-42', 'roles' => ['editor'], 'tenant' => 't1'],
    'can_update',
    ['type' => 'article', 'id' => 'A-77', 'tenant' => 't1'],
    ['tenant' => 't1'],
    [
        ['name' => 'tenant-boundary', 'allow' => true, 'reason' => 'tenant match'],
        ['name' => 'role-voter', 'allow' => true, 'reason' => 'role editor can update', 'ruleId' => 'r_edit_editor'],
        ['name' => 'attribute-voter', 'allow' => false, 'reason' => 'state=draft required'],
    ]
);

$result = new DecisionResult(true, 'v2.0.0', 'r_edit_editor', ['mask' => ['redact' => ['email']]], ['latencyMs' => 2]);
$explain = $explainer->explain($input, $result);

try {
    $record = new DecisionRecord('dec_' . bin2hex(random_bytes(6)), $input, $result, $explain);
} catch (Exception $e) {
}
$logger->log($record);

file_put_contents($reportDir . '/explain_demo.json', json_encode($explain, JSON_PRETTY_PRINT));
echo "explain_demo.json and audit.ndjson written\n";
