<?php
declare(strict_types=1);

namespace App\Legacy\Http\V2;

use App\Service\Admin\RebacStatsService;
use App\Legacy\Metrics\Admin\AdminMetrics;
use App\Legacy\Security\Admin\AdminTokenGuard;
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
     * @param \App\Legacy\Security\Admin\AdminTokenGuard $guard
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
