#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infra\Cache\InMemoryCache;
use App\Service\Pdp\Cache\PdpCache;
use App\ServiceInterface\Pdp\PolicyDecisionProviderInterface;

require_once __DIR__ . '/../../src/ServiceInterface/Role/Pdp/PolicyDecisionProviderInterface.php';
require_once __DIR__ . '/../../src/Service/Role/Pdp/Cache/PdpCache.php';
require_once __DIR__ . '/../../src/InfraInterface/Cache/CacheInterface.php';
require_once __DIR__ . '/../../src/Infra/Cache/InMemoryCache.php';

/**
 *
 */

/**
 *
 */
final class DummyProvider implements PolicyDecisionProviderInterface
{
    /**
     * @param array $subject
     * @param string $action
     * @param array $resource
     * @param array $context
     * @return bool
     */
    public function isAllowed(array $subject, string $action, array $resource, array $context = []): bool
    {
        return ($subject['id'] ?? '') === ($resource['ownerId'] ?? null);
    }
}

$reportDir = __DIR__ . '/../../report';
@mkdir($reportDir, 0775, true);

$inner = new DummyProvider();
$cache = new InMemoryCache();
$pdp = new PdpCache($inner, $cache, 300);

$cases = [
    [['id' => 'u1'], 'can_read', ['type' => 'order', 'id' => 'o1', 'ownerId' => 'u1'], []],
    [['id' => 'u2'], 'can_read', ['type' => 'order', 'id' => 'o1', 'ownerId' => 'u1'], []],
    [['id' => 'u1'], 'can_read', ['type' => 'order', 'id' => 'o1', 'ownerId' => 'u1'], []], // cached
];

$out = [];
foreach ($cases as $i => $c) {
    $out[] = [
        'case' => $i + 1,
        'allowed' => $pdp->isAllowed($c[0], $c[1], $c[2], $c[3]),
    ];
}
file_put_contents($reportDir . '/pdp_cache_smoke.json', json_encode(['created' => date('c'), 'cases' => $out], JSON_PRETTY_PRINT));
echo "pdp_cache_smoke.json written\n";
