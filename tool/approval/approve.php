#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infrastructure\Approval\FileApprovalStore;
use App\Service\Approval\ApprovalGate;

require_once __DIR__ . '/../../vendor/autoload.php';

$id = $argv[1] ?? null;
$actor = $argv[2] ?? 'approver';

if (!$id) {
    fwrite(STDERR, "usage: approve.php <approvalId> [actorId]\n");
    exit(2);
}

$store = new FileApprovalStore();
$gate = new ApprovalGate($store);
$store->approve($id, ['id' => $actor, 'reason' => 'manual approve']);
$final = $gate->resolve($id);
echo json_encode(['final' => $final], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
