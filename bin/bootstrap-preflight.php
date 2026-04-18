<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);

require $projectRoot . '/bin/bootstrap-runtime-requirements.php';

$status = role_runtime_requirement_status($projectRoot);

if ($status['vendor_autoload_present'] && $status['missing_extensions'] === []) {
    return;
}

fwrite(STDERR, implode(PHP_EOL, role_runtime_requirement_messages($status)) . PHP_EOL);
exit(1);
