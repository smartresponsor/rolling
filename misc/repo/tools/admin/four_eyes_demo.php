#!/usr/bin/env php
<?php
declare(strict_types=1);

use Admin\Action\GrantRoleAction;
use Admin\ApprovalWorkflow;
use Admin\Guard\FourEyesApprovalGuard;
use App\Infra\Role\Admin\InMemoryApprovalRequestRepository;

require_once __DIR__ . '/../../src/Infra/Role/Admin/InMemoryApprovalRequestRepository.php';
require_once __DIR__ . '/../../src/Service/Role/Admin/Guard/FourEyesApprovalGuard.php';
require_once __DIR__ . '/../../src/Service/Role/Admin/Action/GrantRoleAction.php';
require_once __DIR__ . '/../../src/Service/Role/Admin/ApprovalWorkflow.php';

$reportDir = __DIR__ . '/../../report';
@mkdir($reportDir, 0775, true);

$repo = new InMemoryApprovalRequestRepository();
$guard = new FourEyesApprovalGuard();
$applier = new GrantRoleAction($reportDir);
$flow = new ApprovalWorkflow($repo, $guard, $applier);

$req = $flow->create('owner', 'user-42', 'role.admin', 't1');
echo "created: {$req->id}\n";

$req = $flow->approve($req->id, 'approver_A');
echo "after A: status={$req->status}, approvals=" . count($req->approvers) . "\n";

$req = $flow->approve($req->id, 'approver_B');
echo "after B: status={$req->status}, approvals=" . count($req->approvers) . "\n";

$report = $reportDir . '/grants_applied.ndjson';
if (file_exists($report)) {
    echo "applied→ " . trim(file_get_contents($report)) . "\n";
} else {
    echo "no grant applied\n";
}
