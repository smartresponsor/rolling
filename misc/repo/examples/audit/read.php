<?php

declare(strict_types=1);

require __DIR__ . '/../../src/Service/Audit/Redactor.php';
require __DIR__ . '/../../src/Service/Audit/Logger.php';

use App\Service\Audit\Logger;

$l = new Logger(__DIR__ . '/../../var/log/role');
echo json_encode($l->tail(10), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
