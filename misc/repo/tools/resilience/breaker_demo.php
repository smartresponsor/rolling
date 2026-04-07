#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Service\Resilience\Backoff\ExponentialJitterBackoff;
use App\Service\Resilience\CircuitBreaker\SimpleCircuitBreaker;
use App\Service\Resilience\ResilientInvoker;
use App\Service\Resilience\Time\SystemClock;
use App\Service\Resilience\Time\SystemSleeper;

require_once __DIR__ . '/../../src/Service/Resilience/Time/SystemClock.php';
require_once __DIR__ . '/../../src/Service/Resilience/Time/SystemSleeper.php';
require_once __DIR__ . '/../../src/Service/Resilience/Backoff/ExponentialJitterBackoff.php';
require_once __DIR__ . '/../../src/Service/Resilience/CircuitBreaker/SimpleCircuitBreaker.php';
require_once __DIR__ . '/../../src/Service/Resilience/ResilientInvoker.php';

$reportDir = __DIR__ . '/../../report';
@mkdir($reportDir, 0775, true);

$clock = new SystemClock();
$sleeper = new SystemSleeper();
$backoff = new ExponentialJitterBackoff(50, 300);
$breaker = new SimpleCircuitBreaker($clock, threshold: 3, windowMs: 5000, coolDownMs: 500);
$invoker = new ResilientInvoker($breaker, $backoff, $clock, $sleeper);

// flaky function: fails twice then succeeds
$state = ['n' => 0];
$fn = function () use (&$state) {
    $state['n']++;
    if ($state['n'] <= 2) {
        throw new RuntimeException("Transient fail #{$state['n']}", 503);
    }
    return "OK@" . $state['n'];
};

$result = null;
$error = null;
try {
    $result = $invoker->invoke($fn, ['maxAttempts' => 5]);
} catch (Throwable $e) {
    $error = $e->getMessage();
}

$data = [
    'result' => $result,
    'error' => $error,
    'breaker' => $breaker->getMetrics(),
    'attempts' => $state['n'],
    'ts' => date('c'),
];

file_put_contents($reportDir . '/breaker_demo.json', json_encode($data, JSON_PRETTY_PRINT));
echo "breaker_demo.json written\n";
