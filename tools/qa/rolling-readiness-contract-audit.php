<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__, 2);
$producerPath = $projectRoot . '/tools/qa/dependency-readiness.php';
$consumerPath = $projectRoot . '/tools/qa/current-summary.php';

$producer = is_file($producerPath) ? (string) file_get_contents($producerPath) : '';
$consumer = is_file($consumerPath) ? (string) file_get_contents($consumerPath) : '';

$requiredKeys = [
    'ready_for_bootstrap',
    'missing_extensions',
    'composer_binary_present',
    'vendor_autoload_present',
];

$legacyKeys = [
    'composer_on_path',
    'vendor_autoload_exists',
];

$missingProducerKeys = [];
$missingConsumerKeys = [];
$legacyConsumerKeys = [];

foreach ($requiredKeys as $key) {
    if (!str_contains($producer, "'{$key}'")) {
        $missingProducerKeys[] = $key;
    }

    if (!str_contains($consumer, "['{$key}']")) {
        $missingConsumerKeys[] = $key;
    }
}

foreach ($legacyKeys as $key) {
    if (str_contains($consumer, "['{$key}']")) {
        $legacyConsumerKeys[] = $key;
    }
}

$result = [
    'contract' => 'dependency-readiness producer to current-summary consumer',
    'required_keys' => $requiredKeys,
    'missing_producer_keys' => $missingProducerKeys,
    'missing_consumer_keys' => $missingConsumerKeys,
    'legacy_consumer_keys' => $legacyConsumerKeys,
    'ok' => $missingProducerKeys === [] && $missingConsumerKeys === [] && $legacyConsumerKeys === [],
];

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

exit($result['ok'] ? 0 : 1);
