#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Service\Admin\Action\GrantRoleAction;
use App\Service\Admin\ApprovalWorkflow;
use App\Service\Admin\Guard\FourEyesApprovalGuard;
use App\Infrastructure\Admin\InMemoryApprovalRequestRepository;

require_once __DIR__ . '/../../vendor/autoload.php';

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
