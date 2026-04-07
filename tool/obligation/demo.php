#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infrastructure\Audit\FileAuditTrail;
use App\Service\Mask\DataMasker;
use App\Service\Obligation\BasicObligationRunner;

require_once __DIR__ . '/../../src/Infra/Role/Audit/FileAuditTrail.php';
require_once __DIR__ . '/../../src/Service/Role/Mask/DataMasker.php';
require_once __DIR__ . '/../../src/Service/Role/Obligation/BasicObligationRunner.php';
require_once __DIR__ . '/../../src/ServiceInterface/Role/Audit/AuditTrailInterface.php';
require_once __DIR__ . '/../../src/ServiceInterface/Role/Mask/DataMaskerInterface.php';
require_once __DIR__ . '/../../src/ServiceInterface/Role/Obligation/ObligationRunnerInterface.php';

@mkdir(__DIR__ . '/../../report', 0775, true);

$runner = new BasicObligationRunner(new FileAuditTrail(), new DataMasker());

$decision = ['allowed' => true, 'action' => 'read', 'obligations' => ['audit.access', 'mask.email:redact', 'mask.phone:last4']];
$subject = ['id' => 'u-1', 'role' => 'support'];
$resource = ['id' => 'r-42', 'type' => 'doc', 'email' => 'user@example.com', 'phone' => '2815551234'];

$out = $runner->apply($decision, $subject, $resource);
file_put_contents(__DIR__ . '/../../report/obligation_demo.json', json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "report/obligation_demo.json written\n";
