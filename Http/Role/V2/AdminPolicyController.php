<?php
declare(strict_types=1);

namespace Http\Role\V2;

use App\Metrics\Role\Admin\AdminMetrics;
use Policy\Role\Registry\RegistryService;
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
final class AdminPolicyController
{
    /**
     * @param \src\Security\Role\Admin\AdminTokenGuard $guard
     * @param \Policy\Role\Registry\RegistryService $svc
     */
    public function __construct(private readonly AdminTokenGuard $guard, private readonly RegistryService $svc)
    {
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $r
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function import(Request $r): JsonResponse
    {
        try {
            $this->guard->assert($r);
            /** @var array{ns:string,name:string,version:string,doc:string} $in */
            $in = json_decode((string)$r->getContent(), true) ?? [];
            $ns = (string)$in['ns'];
            $name = (string)$in['name'];
            $ver = (string)$in['version'];
            $doc = (string)$in['doc'];
            $tok = $this->svc->importPolicy($ns, $name, $ver, $doc);
            AdminMetrics::inc('role_admin_policy_import_total');
            return new JsonResponse(['ok' => true, 'rev' => (string)$tok]);
        } catch (Throwable $e) {
            AdminMetrics::inc('role_admin_errors_total');
            return new JsonResponse(['ok' => false, 'error' => $e->getMessage()], 401);
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $r
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function activate(Request $r): JsonResponse
    {
        try {
            $this->guard->assert($r);
            /** @var array{ns:string,name:string,version:string} $in */
            $in = json_decode((string)$r->getContent(), true) ?? [];
            $ns = (string)$in['ns'];
            $name = (string)$in['name'];
            $ver = (string)$in['version'];
            $tok = $this->svc->activatePolicy($ns, $name, $ver);
            AdminMetrics::inc('role_admin_policy_activate_total');
            return new JsonResponse(['ok' => true, 'rev' => (string)$tok]);
        } catch (Throwable $e) {
            AdminMetrics::inc('role_admin_errors_total');
            return new JsonResponse(['ok' => false, 'error' => $e->getMessage()], 401);
        }
    }

    public function list(Request $r): JsonResponse
    {
        try {
            $this->guard->assert($r);
            $ns = (string)$r->query->get('ns');
            $name = (string)$r->query->get('name');
            $rows = array_map(fn($rec) => ['ns' => $rec->ns, 'name' => $rec->name, 'version' => $rec->version, 'is_active' => $rec->isActive, 'created_at' => $rec->createdAt], $this->svc->listVersions($ns, $name));
            return new JsonResponse(['ok' => true, 'versions' => $rows]);
        } catch (Throwable $e) {
            AdminMetrics::inc('role_admin_errors_total');
            return new JsonResponse(['ok' => false, 'error' => $e->getMessage()], 401);
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $r
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function export(Request $r): JsonResponse
    {
        try {
            $this->guard->assert($r);
            $ns = (string)$r->query->get('ns');
            $name = (string)$r->query->get('name');
            $ver = (string)$r->query->get('version');
            $doc = $this->svc->exportPolicy($ns, $name, $ver);
            if ($doc === null) return new JsonResponse(['ok' => false, 'error' => 'not_found'], 404);
            return new JsonResponse(['ok' => true, 'doc' => json_decode($doc, true)]);
        } catch (Throwable $e) {
            AdminMetrics::inc('role_admin_errors_total');
            return new JsonResponse(['ok' => false, 'error' => $e->getMessage()], 401);
        }
    }
}
