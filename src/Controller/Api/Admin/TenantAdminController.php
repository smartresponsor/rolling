<?php
declare(strict_types=1);

namespace App\Controller\Api\Admin;

use App\Legacy\Security\Admin\Voter;
use App\Legacy\Security\Admin\Roles;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Tenant\{Quota};
use App\Service\Tenant\Backup;
use App\Service\Tenant\Limits;
use App\Service\Tenant\Restore;

/**
 *
 */

/**
 *
 */
final class TenantAdminController
{
    private Voter $voter;
    private Quota $quota;
    private Limits $limits;
    private Backup $backup;
    private Restore $restore;

    /**
     * @param string $secretPath
     * @param string $varDir
     */
    public function __construct(
        string $secretPath = __DIR__ . '/../../../../../var/admin_secret.txt',
        string $varDir = __DIR__ . '/../../../../../var'
    )
    {
        $this->voter = new Voter($secretPath);
        $this->quota = new Quota($varDir . '/tenants');
        $this->limits = new Limits($varDir . '/tenants');
        $this->backup = new Backup($varDir, $varDir . '/backup');
        $this->restore = new Restore($varDir);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function quotaGet(Request $req): JsonResponse
    {
        if (!$this->voter->isAdmin($req)) {
            return new JsonResponse(['ok' => false, 'error' => 'forbidden'], 403);
        }
        $tenant = (string)($req->query->get('tenant') ?? '');
        if ($tenant === '') return new JsonResponse(['ok' => false, 'error' => 'tenant required'], 400);
        $limit = $this->quota->getLimit($tenant);
        return new JsonResponse(['ok' => true, 'tenant' => $tenant, 'limit' => $limit]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function quotaSet(Request $req): JsonResponse
    {
        if (!$this->voter->isAdmin($req)) {
            return new JsonResponse(['ok' => false, 'error' => 'forbidden'], 403);
        }
        $payload = json_decode((string)$req->getContent(), true) ?? [];
        $tenant = (string)($payload['tenant'] ?? '');
        $perMin = (int)($payload['per_min'] ?? 0);
        if ($tenant === '' || $perMin <= 0) return new JsonResponse(['ok' => false, 'error' => 'tenant/per_min required'], 400);
        $this->quota->setLimit($tenant, $perMin);
        return new JsonResponse(['ok' => true, 'tenant' => $tenant, 'limit' => ['limit_per_min' => $perMin]]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function backup(Request $req): JsonResponse
    {
        if (!$this->voter->isAdmin($req)) {
            return new JsonResponse(['ok' => false, 'error' => 'forbidden'], 403);
        }
        $payload = json_decode((string)$req->getContent(), true) ?? [];
        $tenant = (string)($payload['tenant'] ?? '');
        if ($tenant === '') return new JsonResponse(['ok' => false, 'error' => 'tenant required'], 400);
        $res = $this->backup->run($tenant);
        return new JsonResponse($res, $res['ok'] ? 200 : 500);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function restore(Request $req): JsonResponse
    {
        if (!$this->voter->isAdmin($req, [Roles::OWNER])) {
            return new JsonResponse(['ok' => false, 'error' => 'forbidden'], 403);
        }
        $payload = json_decode((string)$req->getContent(), true) ?? [];
        $path = (string)($payload['path'] ?? '');
        if ($path === '' || !file_exists($path)) return new JsonResponse(['ok' => false, 'error' => 'valid path required'], 400);
        $res = $this->restore->run($path);
        return new JsonResponse($res, $res['ok'] ? 200 : 500);
    }
}
