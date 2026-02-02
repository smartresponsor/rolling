<?php
require __DIR__ . '/../../src/Service/Role/Audit/Redactor.php';
require __DIR__ . '/../../src/Service/Role/Audit/Logger.php';

use Audit\Logger;

$l = new Logger(__DIR__ . '/../../var/log/role');
print json_encode($l->tail(10), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
