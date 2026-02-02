<?php
declare(strict_types=1);

namespace Http\Role\V2;

use Admin\RebacStatsService;
use App\Metrics\Role\Admin\AdminMetrics;
use src\Security\Role\Admin\AdminTokenGuard;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

/**
 *
 */

/**
 *
 */
final class AdminRebacController
{
    /**
     * @param \src\Security\Role\Admin\AdminTokenGuard $guard
     * @param \Admin\RebacStatsService $stats
     */
    public function __construct(
        private readonly AdminTokenGuard   $guard,
        private readonly RebacStatsService $stats
    )
    {
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $r
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function stats(Request $r): JsonResponse
    {
        try {
            $this->guard->assert($r);
            $ns = (string)$r->query->get('ns');
            $out = $this->stats->stats($ns);
            AdminMetrics::inc('role_admin_rebac_stats_total');
            return new JsonResponse(['ok' => true, 'stats' => $out]);
        } catch (Throwable $e) {
            AdminMetrics::inc('role_admin_errors_total');
            return new JsonResponse(['ok' => false, 'error' => $e->getMessage()], 401);
        }
    }
}
