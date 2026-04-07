#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infrastructure\Approval\FileApprovalStore;

require_once __DIR__ . '/../../src/Infra/Role/Approval/FileApprovalStore.php';
require_once __DIR__ . '/../../src/ServiceInterface/Role/Approval/ApprovalStoreInterface.php';

$id = $argv[1] ?? null;
$actor = $argv[2] ?? 'approver';

if (!$id) {
    fwrite(STDERR, "usage: reject.php <approvalId> [actorId]\n");
    exit(2);
}

$store = new FileApprovalStore();
$store->reject($id, ['id' => $actor, 'reason' => 'manual reject']);
echo json_encode(['ok' => true], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
