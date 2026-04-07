#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infrastructure\Approval\FileApprovalStore;
use App\Service\Approval\ApprovalGate;

require_once __DIR__ . '/../../vendor/autoload.php';

@mkdir(__DIR__ . '/../../report', 0775, true);

$store = new FileApprovalStore();
$gate = new ApprovalGate($store);

$subject = ['id' => 'u1', 'roles' => ['user']];
$resource = ['type' => 'doc', 'id' => '1', 'ownerId' => 'u2'];
$decision = ['allowed' => true, 'ruleId' => 'allow.owner.delete', 'reason' => 'owner or admin may delete'];

$res = $gate->gate($decision, $subject, 'delete', $resource);
file_put_contents(__DIR__ . '/../../report/approval_demo_step1.json', json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

if (!empty($res['approvalId'])) {
    // simulate approval
    $id = $res['approvalId'];
    $store->approve($id, ['id' => 'auditor', 'reason' => 'check ok']);
    $final = $gate->resolve($id);
    file_put_contents(__DIR__ . '/../../report/approval_demo_step2.json', json_encode($final, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

echo "report/approval_demo_step1.json and step2.json written\n";
